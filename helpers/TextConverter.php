<?php

namespace afzalroq\cms\helpers;

class TextConverter
{
    public static function convertWithXml(&$reader, &$html, $function)
    {
        $content = $reader->readInnerXml();
        if ($content) {
            $reader = new \XMLREADER();
            $reader->XML($content);
            while ($reader->read()) {
                if ($reader->nodeType == \XMLReader::ELEMENT) {
                    if ($reader->isEmptyElement) {
                        $htmlInner = '<' . $reader->name . ' style = "' . $reader->getAttribute('style') . '" ';
                        if ($reader->name == 'img') {
                            $htmlInner .= ' width="' . $reader->getAttribute('width') . '" height="' . $reader->getAttribute('height') . '" src="' . $reader->getAttribute('src') . '"';
                        }
                        $htmlInner .= ' />';
                        $html .= $htmlInner;
                    } else {
                        $html .= '<' . $reader->name . ' style = "' . $reader->getAttribute('style') . '" ';
                        if ($reader->name == 'a') {
                            $html .= ' href="' . $reader->getAttribute('href') . '"';
                        }
                        $html .= '>';
                    }
                } elseif ($reader->nodeType == \XMLReader::TEXT) {
                    $html .= self::$function($reader->value);
                } elseif ($reader->nodeType == \XMLReader::END_ELEMENT) {
                    $html .= '</' . $reader->name . '>';
                }
            }
        }

    }

    public static function convert($text, $function)
    {
        $html = '';
        $doc = new \DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML(mb_convert_encoding($text, 'HTML-ENTITIES', 'UTF-8'));
        $doc->preserveWhiteSpace = false;
        $doc->formatOutput = true;
        $outXML = $doc->saveXML();
        $reader = new \XMLREADER();
        $reader->XML($outXML);
        while ($reader->read()) {
            self::convertWithXml($reader, $html, $function);
        }
        return $html;
    }
    public static function to_cyrillic($string, $array = []): string
    {
        $first = [
            "o`" => "ў","oʻ" => "ў", "gʻ" => "ғ", "g`" => "ғ" ,"G'" => "Ғ", "G`" => "Ғ", "оʼ" => "ў","o'" => "ў","ye" => "е","yu" => "ю","Yu" => "Ю", "O`" => "Ў", "Oʼ" => "Ў", "O'" => "Ў"
        ];
        $gost = [
            "a" => "а", "b" => "б", "v" => "в", "g" => "г", "d" => "д", "e" => "е", "yo" => "ё",
            "j" => "ж", "z" => "з", "i" => "и", "y" => "й", "k" => "к", "q" => "қ",
            "l" => "л", "m" => "м", "n" => "н", "o" => "о", "p" => "п", "r" => "р", "s" => "с", "t" => "т",
            "f" => "ф", "h" => "ҳ", "c" => "ц",
            "ch" => "ч", "sh" => "ш", "sch" => "щ", "ie" => "ы", "u" => "у", "ya" => "я", "A" => "А", "B" => "Б",
            "V" => "В", "G" => "Г", "D" => "Д", "E" => "Э", "Yo" => "Ё", "J" => "Ж", "Z" => "З", "I" => "И", "Y" => "Й",
            "K" => "К", "L" => "Л", "M" => "М", 'Q' => 'Қ',
            "N" => "Н", "O" => "О", "P" => "П",
            "R" => "Р", "S" => "С", "T" => "Т", "Yu" => "Ю", "F" => "Ф", "H" => "Х", "C" => "Ц", "Ch" => "Ч", "Sh" => "Ш",
            "Sch" => "Щ", "Ie" => "Ы", "U" => "У", "Ya" => "Я", "'" => "ь", "_'" => "Ь", "''" => "ъ", "_''" => "Ъ",
            '&nbsp;' => '&nbsp;',"ʼ" => "ъ",
        ];
        if (!empty($array)) {
            $gost = array_merge($gost, array_combine($array, $array));
        }
        $string = strtr($string, $first);
        return strtr($string, $gost);
    }

    public static function to_latin($string): string
    {
        $gost = [
            "а" => "a", "б" => "b", "в" => "v", "г" => "g", "ғ" => "g`", "д" => "d",
            "е" => "e", "ое" => "oye", "ие" => "iye", "уе" => "uye", "ае" => "aye", "ё" => "yo", "ж" => "j", "з" => "z", "и" => "i",
            "й" => "y", "к" => "k", "қ" => "q", "л" => "l", "м" => "m", "н" => "n",
            "о" => "o", "п" => "p", "р" => "r", "с" => "s", "т" => "t",
            "у" => "u", "ў" => "o`", "ф" => "f", "х" => "x", "ҳ" => "h", "ц" => "ts", "ч" => "ch",
            "ш" => "sh", "щ" => "sch", "ы" => "ie", "э" => "e", "ю" => "yu",
            "я" => "ya",
            "А" => "A", "Б" => "B", "В" => "V", "Г" => "G", "Ғ" => "G`", "Д" => "D",
            "Е" => "Ye", "Ё" => "Yo", "Ж" => "J", "З" => "Z", "И" => "I",
            "Й" => "Y", "К" => "K", "Қ" => "Q", "Л" => "L", "М" => "M", "Н" => "N",
            "О" => "O", "П" => "P", "Р" => "R", "С" => "S", "Т" => "T",
            "У" => "U", "Ф" => "F", "Х" => "H", "Ҳ" => "H", "Ц" => "Ts", "Ч" => "Ch",
            "Ш" => "Sh", "Щ" => "Sch", "Ы" => "Ie", "Э" => "E", "Ю" => "Yu",
            "Я" => "Ya", "Ў" => "O`",
            "ь" => "'", "Ь" => "_'", "ъ" => "'", "Ъ" => "_''"
        ];
        return strtr($string, $gost);
    }


}