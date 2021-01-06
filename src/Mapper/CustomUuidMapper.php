<?php


namespace App\Mapper;


use Doctrine\DBAL\Types\ConversionException;
use InvalidArgumentException;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class CustomUuidMapper
{
    private const REPLACE_SLASH = "-";

    public function fromString($str): UuidInterface
    {
        try {
            return Uuid::fromBytes(base64_decode(str_replace(self::REPLACE_SLASH, "/", $str) . "=="));
        } catch (InvalidArgumentException $e) {
            throw new ConversionException($e);
        }
    }

    public function toString(UuidInterface $uuid): string
    {
        return str_replace("/", self::REPLACE_SLASH, str_replace("==", "", base64_encode($uuid->getBytes())));
    }
}