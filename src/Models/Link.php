<?php

namespace App\Models;

class Link {
    protected $raw;
    protected $shortified;
    protected $isCustom;

    public function __construct($raw, $shortified, $isCustom) {
        $this->raw = $raw;
        $this->shortified = $shortified;
        $this->isCustom = $isCustom;
    }

    public function getPdoStatementParams() {
        return [
            ':shortified' => $this->shortified,
            ':raw' => $this->raw,
            ':is_custom' => $this->isCustom
        ];
    }

    public static function getFromFetchResult($fetchResult) {
        return new static($fetchResult['raw'], $fetchResult['shortified'], $fetchResult['is_custom']);
    }

    public function __get($key) {
        if (isset($this->{$key})) {
            return $this->{$key};
        }

        return null;
    }
}