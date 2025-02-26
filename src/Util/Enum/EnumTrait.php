<?php

namespace App\Util\Enum;

use BackedEnum;
use Symfony\Contracts\Translation\TranslatorInterface;

trait EnumTrait
{
    /**
     * @return string[]
     */
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    /**
     * @return string[]
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * @return string[]
     */
    public static function array(): array
    {
        return array_combine(self::values(), self::names());
    }

    public static function containValue(string $value): bool
    {
        return in_array($value, self::values());
    }

    public static function containName(string $name): bool
    {
        return in_array($name, self::names());
    }

    /**
     * @return mixed[]
     */
    public static function casesTranslated(TranslatorInterface $translator = NULL, string $domainTranslation = NULL): array
    {
        $constantsTranslated = self::getConstants($translator, $domainTranslation);
        return array_combine($constantsTranslated, self::cases());
    }

    /**
     * @return string[]
     */
    public static function getFormValues(?TranslatorInterface $translator = NULL, ?string $domainTranslation = NULL): array
    {
        $constants = self::getConstants();
        $constantsTranslated = self::getConstants($translator, $domainTranslation);
        return array_combine($constantsTranslated, $constants);
    }

    public static function fromName(string $name): ?BackedEnum
    {
        $data = array_combine(self::names(), self::values());
        if(array_key_exists($name, $data)) {
            return self::from($data[$name]);
        }
        return self::tryFrom($name);
    }

    /**
     * @return string[]
     */
    private static function getConstants(?TranslatorInterface $translator = NULL, ?string $domainTranslation = NULL): array
    {
        if($domainTranslation != NULL) {
            $constants = self::values();
            $constantsTranslated = array();
            foreach($constants as $constant) {
                $constantsTranslated[] = $translator->trans($constant, [], $domainTranslation);
            }
            return $constantsTranslated;
        } else {
            return self::values();
        }
    }
}