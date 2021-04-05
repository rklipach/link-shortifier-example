<?php

namespace App\Controllers;

use App\Database\Repositories\LinkRepository;
use App\Models\Link;

class AppController {
    public static function index() {
        require 'views/index.html';
    }

    public static function renderResult() {
        include 'views/result.php';
    }

    public static function redirectFromShortified() {
        $shortified = basename($_SERVER['REQUEST_URI']);
        $link = (new LinkRepository())->fetchByShortified($shortified);

        header("HTTP/1.1 301 Moved Permanently");
        static::setLocationHeader(is_null($link) ? '/' : $link->raw);
        exit();
    }

    public static function shortify() {
        $linkRepository = new LinkRepository();
        $shortLinkLength = 7;
        $postRawUrl = $_POST['url'];

        $postUrlHash = sha1($postRawUrl);
        $desiredShortified = isset($_POST['desired_short_url']) && !empty($_POST['desired_short_url']) ? $_POST['desired_short_url'] : null;

        if ($desiredShortified === 'result') {
            // Reserved word
            $desiredShortified = null;
        }

        $shortified = substr($postUrlHash, 0, $shortLinkLength);

        $isNewShortifiedShouldBeCreated = true;
        $isCustom = 0;
        $isShortifiedDetected = false;

        if (!is_null($desiredShortified)) {
            $linkResult = $linkRepository->fetchByShortified($desiredShortified);

            if (is_null($linkResult)) {
                $shortified = $desiredShortified;
                $isCustom = 1;
                $isShortifiedDetected = true;
            }
        }

        while (!$isShortifiedDetected) {
            $sameShortLinkWithDifferentSourceFound = false;

            foreach ($linkRepository->fetchAllNonCustomLinksByRawOrShortified($postRawUrl, $shortified) as $item) {
                /** @var Link $item */
                if ($item->raw === $postRawUrl) {
                    $shortified = $item->shortified;
                    $isShortifiedDetected = true;
                    $isNewShortifiedShouldBeCreated = false;
                    break;
                } else if ($item->raw !== $postRawUrl && $item->shortified === $shortified) {
                    // Needed to avoid potential collisions
                    $shortLinkLength += 1;
                    $shortified = substr($postUrlHash, 0, $shortLinkLength);
                    $sameShortLinkWithDifferentSourceFound = true;
                }
            }

            if (!$sameShortLinkWithDifferentSourceFound) {
                $isShortifiedDetected = true;
            }
        }

        if ($isNewShortifiedShouldBeCreated) {
            $linkRepository->insert(new Link($postRawUrl, $shortified, $isCustom));
        }

        $encodedLink = base64_encode(static::getNewShortifiedLink($shortified));
        static::setLocationHeader("/result?r=$encodedLink");
    }

    protected static function getNewShortifiedLink($shortified) {
        $serverUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}";
        return "$serverUrl/$shortified";
    }

    protected static function setLocationHeader($location) {
        header("Location: $location");
    }
}