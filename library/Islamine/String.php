<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of String
 *
 * @author jeremie
 */
class Islamine_String {

    public static function replaceNewLinesWithBr($string) {
        return str_replace(array('\r\n', '\n'), '', nl2br($string, false));
    }
    
    public static function textWrap($string, $width) {
        $stripped = trim(strip_tags($string));
        if (mb_strlen($stripped, 'utf-8') > $width) {
            $stripped = wordwrap($stripped, $width, '<br>');
            $stripped = mb_substr($stripped, 0, mb_strpos($stripped, "<br>", 0, 'utf-8'), 'utf-8');
            $stripped .= '...';
        }
        return $stripped;
    }

    /**
     * Multibyte capable wordwrap
     *
     * @param string $str
     * @param int $width
     * @param string $break
     * @return string
     */
    public static function mb_wordwrap($str, $width = 74, $break = "\r\n") {
        // Return short or empty strings untouched
        if (empty($str) || mb_strlen($str, 'UTF-8') <= $width)
            return $str;

        $br_width = mb_strlen($break, 'UTF-8');
        $str_width = mb_strlen($str, 'UTF-8');
        $return = '';
        $last_space = false;

        for ($i = 0, $count = 0; $i < $str_width; $i++, $count++) {
            // If we're at a break
            if (mb_substr($str, $i, $br_width, 'UTF-8') == $break) {
                $count = 0;
                $return .= mb_substr($str, $i, $br_width, 'UTF-8');
                $i += $br_width - 1;
                continue;
            }

            // Keep a track of the most recent possible break point
            if (mb_substr($str, $i, 1, 'UTF-8') == " ") {
                $last_space = $i;
            }

            // It's time to wrap
            if ($count > $width) {
                // There are no spaces to break on!  Going to truncate :(
                if (!$last_space) {
                    $return .= $break;
                    $count = 0;
                } else {
                    // Work out how far back the last space was
                    $drop = $i - $last_space;

                    // Cutting zero chars results in an empty string, so don't do that
                    if ($drop > 0) {
                        $return = mb_substr($return, 0, -$drop);
                    }

                    // Add a break
                    $return .= $break;

                    // Update pointers
                    $i = $last_space + ($br_width - 1);
                    $last_space = false;
                    $count = 0;
                }
            }

            // Add character from the input string to the output
            $return .= mb_substr($str, $i, 1, 'UTF-8');
        }
        return $return;
    }

}

?>
