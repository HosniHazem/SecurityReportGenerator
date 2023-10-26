<?php
// app/CustomPhpWord/CustomPhpWord.php

namespace App\CustomPhpWord;

use PhpOffice\PhpWord\PhpWord as BasePhpWord;
use PhpOffice\PhpWord\Shared\Html;

class CustomPhpWord extends BasePhpWord
{
    public static function getXmlFromHtml($html, $fullHTML = false, $preserveWhiteSpace = true, $options = null)
    {
        $html = self::preprocessHtml($html, $fullHTML);

        // Load DOM
        $dom = new \DOMDocument();
        $dom->preserveWhiteSpace = $preserveWhiteSpace;
        $dom->loadXML($html);
        $xpath = new \DOMXPath($dom);
        $node = $dom->getElementsByTagName('body');

        $resultXml = '';
        self::parseNode($node->item(0), $resultXml);

        return $resultXml;
    }

    private static function preprocessHtml($html, $fullHTML)
    {
        // ... (same as before)

        if (false === $fullHTML) {
            $html = '<body>' . $html . '</body>';
        }

        return $html;
    }

    private static function parseNode($node, &$resultXml)
    {
        foreach ($node->childNodes as $child) {
            if ($child->nodeName === 'p') {
                $resultXml .= '<w:p><w:pPr/>';
                self::parseNode($child, $resultXml);
                $resultXml .= '</w:p>';
            } elseif ($child->nodeName === 'strong') {
                $resultXml .= '<w:r><w:rPr><w:b w:val="1"/><w:bCs w:val="1"/></w:rPr>';
                self::parseNode($child, $resultXml);
                $resultXml .= '</w:r>';
            } elseif ($child->nodeName === '#text') {
                $resultXml .= '<w:t xml:space="preserve">' . $child->nodeValue . '</w:t>';
            } elseif ($child->nodeName === 'img') {
                // Example: Convert <img> to <w:pict> with image data
                $src = $child->getAttribute('src');
                $resultXml .= '<w:pict><v:shape type="#_x0000_t75" stroked="f" style="width:714px; height:293px; margin-left:0px; margin-top:0px; mso-position-horizontal:left; mso-position-vertical:top; mso-position-horizontal-relative:char; mso-position-vertical-relative:line;"><w10:wrap type="inline"/><v:imagedata r:id="rId7" o:title=""/></v:shape></w:pict>';
            } elseif ($child->nodeName === 'a') {
                // Example: Handle <a> (hyperlink) element
                $href = $child->getAttribute('href');
                $resultXml .= '<w:hyperlink w:anchor="' . $href . '">';
                self::parseNode($child, $resultXml);
                $resultXml .= '</w:hyperlink>';
            } else {
                // Handle other HTML elements
                // Modify $resultXml accordingly
            }
        }
    }
}
