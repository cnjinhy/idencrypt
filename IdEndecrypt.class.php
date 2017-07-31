<?php

/**
 * 加解密数字格式的ID,例如将 123456 加密为 38654321894938303106 格式.
 * 加密过程中有校验防篡改功能,加密完成的字符串中修改任意一位均不能正常解密
 * 在应用过程中可以防止爬虫按递增规律抓取
 * 实现效果类似这种格式的网址 
 * 
 * encrypt and decrypt numeric ID, for example, encrypt 123456 to 38654321894938303106
 * has the function of checking tamper proofing, any modified of encrypted  the string in the string can not be decrypted
 * in the application process, the crawler can be prevented from crawling by increasing regularity
 * achieve effects similar to URLs in this format 
 * 
 * https://zhidao.baidu.com/question/629747168174413524.html
 * https://zhidao.baidu.com/question/1929987305252691987.html
 * @author jinhongyu <jinhongyu@vip.qq.com>
 * 
 */
class IdEndecrypt {

    const ID_ENCODE_LENGTH = 20;

    /**
     * 检查一个ID是加密的还是非加密的
     * @param type $str
     * @param type $length
     * @return boolean
     */
    public static function isEncode($str, $length = self::ID_ENCODE_LENGTH) {
        if (strlen($str) == $length && is_numeric($str)) {
            return true;
        }
        return false;
    }

    /**
     * 解密ID
     * @param type $encode_id_str
     * @param type $key
     * @return boolean
     */
    public static function decode($encode_id_str, $key = 'TEST123') {
        $int = substr($encode_id_str, 0, 1);
        switch ($int) {
            case 6;
                $sub_start = 2;
                $sub_length = 2;
                $substr_id_start = $int - 1;
                $hash_sub_start = 4;
                break;
            case 5;
                $sub_start = 1;
                $sub_length = 2;
                $substr_id_start = $int - 1;
                $hash_sub_start = 3;
                break;
            case 4;
                $sub_start = 1;
                $sub_length = 2;
                $substr_id_start = $int - 1;
                $hash_sub_start = -1;
                break;
            case 3;
                $sub_start = -2;
                $sub_length = 2;
                $substr_id_start = $int - 1;
                $hash_sub_start = 1;
                break;
            case 2;
                $sub_start = -3;
                $sub_length = 2;
                $substr_id_start = $int - 1;
                $hash_sub_start = -1;
                break;
            case 1;
                $sub_start = -4;
                $sub_length = 2;
                $substr_id_start = $int;
                $hash_sub_start = -2;
                break;
        }
        $substr_id_length = substr($encode_id_str, $sub_start, $sub_length);
        $hash_number = substr($encode_id_str, $hash_sub_start, 1);

        $string_no_hash_1 = substr($encode_id_str, 0, $hash_sub_start);
        $b_start = $hash_sub_start + 1;
        if ($b_start) {
            $string_no_hash_2 = substr($encode_id_str, $b_start);
        } else {
            $string_no_hash_2 = '';
        }
        $string_no_hash = $string_no_hash_1 . $string_no_hash_2;
        $md5_1 = md5($string_no_hash . $key);
        $intori_md5 = preg_replace('#[a-z]#', '', $md5_1);
        $j = substr($intori_md5, 0, 1);
        if ($j != $hash_number) {
            return false;
        }
        return strrev(substr($encode_id_str, $substr_id_start, $substr_id_length));
    }

    /**
     * 加密ID
     * @param type $decode_id
     * @param type $key
     * @param type $max_strsize
     * @return type
     */
    public static function encode($decode_id, $key = 'TEST123', $max_strsize = self::ID_ENCODE_LENGTH) {
        $strlen = $strlen_str = strlen($decode_id);
        if ($strlen < 10) {
            $strlen_str = '0' . $strlen;
        }
        $decode_id_strrev = strrev($decode_id);
        $md5 = md5($decode_id . $key);
        $azori = preg_replace('#[0-9]#', '', $md5);
        $intori = preg_replace('#[a-z]#', '', $md5);
        $array = array(
            'a' => 6,
            'b' => 5,
            'c' => 4,
            'd' => 1,
            'e' => 2,
            'f' => 3,
        );
        $int = $array[substr($azori, 0, 1)];
        $new_id_str = $int;
        switch ($int) {
            case 6;
                $new_id_str .= substr($intori, 0, 1) . $strlen_str . 'J' . $decode_id_strrev . 'X';
                break;
            case 5;
                $new_id_str .= $strlen_str . 'J' . $decode_id_strrev . 'X';
                break;
            case 4;
                $new_id_str .= $strlen_str . $decode_id_strrev . 'X' . 'J';
                break;
            case 3;
                $new_id_str .= 'J' . $decode_id_strrev . 'X' . $strlen_str;
                break;
            case 2;
                $new_id_str .= $decode_id_strrev . 'X' . $strlen_str . 'J';
                break;
            case 1;
                $new_id_str .= $decode_id_strrev . 'X' . $strlen_str . 'J' . substr($intori, 1, 1);
                break;
        }
        $new_strlen = strlen($new_id_str) - 1;
        $pad_strlen = $max_strsize - $new_strlen;
        $intori_strlen = strlen($intori);
        if ($pad_strlen > $intori_strlen) {
            $pad_string = $intori . strrev($intori) . $intori;
        } else {
            $pad_string = $intori;
        }
        $x_replace = substr($pad_string, 0, $pad_strlen);
        $beforehash_with_j = str_replace('X', $x_replace, $new_id_str);
        $hash_string = str_replace('J', '', $beforehash_with_j);
        $md5_1 = md5($hash_string . $key);
        $intori_md5 = preg_replace('#[a-z]#', '', $md5_1);
        $j = substr($intori_md5, 0, 1);
        return str_replace('J', $j, str_replace('X', $x_replace, $new_id_str));
    }

}
