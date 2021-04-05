<?php

namespace App\Database\Repositories;

use \App\Database\DatabaseConnection;
use \App\Models\Link;

class LinkRepository {
    /**
     * @param Link $link
     * @return void
     */
    public function insert(Link $link) {
        $rs = DatabaseConnection::getInstance()->prepare(
            "INSERT INTO links (raw, shortified, is_custom) values (:raw, :shortified, :is_custom)"
        );
        $rs->execute($link->getPdoStatementParams());
    }

    /**
     * @param $raw
     * @param $shortified
     * @return array<Link>
     */
    public function fetchAllNonCustomLinksByRawOrShortified($raw, $shortified) {
        $rs = DatabaseConnection::getInstance()->prepare(
            'SELECT * FROM links WHERE (shortified = :shortified OR raw = :raw) AND is_custom != 1'
        );
        $rs->execute([
            ':shortified' => $shortified,
            ':raw' => $raw
        ]);

        $linksResult = [];
        foreach ($rs->fetchAll() as $linkDb) {
            $linksResult[] = Link::getFromFetchResult($linkDb);
        }

        return $linksResult;
    }

    /**
     * @param $shortified
     * @return Link|null
     */
    public function fetchByShortified($shortified) {
        $rs = DatabaseConnection::getInstance()->prepare('SELECT * FROM links WHERE shortified = :shortified LIMIT 1');
        $rs->execute([ ':shortified' => $shortified ]);
        $result = $rs->fetch();
        return $result === false || is_null($result) ? null : Link::getFromFetchResult($result);
    }
}