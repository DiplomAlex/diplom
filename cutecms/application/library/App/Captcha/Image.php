<?php

class App_Captcha_Image extends Zend_Captcha_Image
{

    static $Numbers = array('1','2','3','4','5','6','7','8','9');

    protected $digitsOnly = FALSE;

    protected $_height = 30;
    protected $_width = 95;

    protected $_fsize = 14;

    protected $_dotNoiseLevel = 10;
    protected $_lineNoiseLevel = 1;

    /**
     * Generate new random word
     *
     * @return string
     */
    protected function _generateWord()
    {
        $word       = '';
        $wordLen    = $this->getWordLen();

        if (isset($this->_options['digitsOnly']) AND ($this->_options['digitsOnly'] === TRUE)) {
            $vowels     = self::$Numbers;
            $consonants = self::$Numbers;
        }
        else {
            $vowels     = $this->_useNumbers ? self::$VN : self::$V;
            $consonants = $this->_useNumbers ? self::$CN : self::$C;
        }

        for ($i=0; $i < $wordLen; $i = $i + 2) {
            // generate word with mix of vowels and consonants
            $consonant = $consonants[array_rand($consonants)];
            $vowel     = $vowels[array_rand($vowels)];
            $word     .= $consonant . $vowel;
        }

        if (strlen($word) > $wordLen) {
            $word = substr($word, 0, $wordLen);
        }

        return $word;
    }


    public function render(Zend_View_Interface $view = NULL, $element = NULL)
    {
        return '<img alt="'.$this->getImgAlt().'" src="' . $this->getImgUrl() . $this->getId() . $this->getSuffix() . '" style="float: left; margin-right: 5px" width="95" height="24"/>';
    }


    protected function _randomSize()
    {
        return mt_rand(300, 300) / 100;
    }


}