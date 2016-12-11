<?php

namespace YoHang88\LetterAvatar;

use Intervention\Image\ImageManager;

class LetterAvatar
{
    /**
     * @var string
     */
    protected $name;


    /**
     * @var string
     */
    protected $name_initials;


    /**
     * @var string
     */
    protected $shape;


    /**
     * @var int
     */
    protected $size;

    /**
     * @var ImageManager
     */
    protected $image_manager;


    public function __construct($name, $shape = 'circle', $size = '48')
    {
        $this->setName($name);
        $this->setImageManager(new ImageManager());
        $this->setShape($shape);
        $this->setSize($size);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return ImageManager
     */
    public function getImageManager()
    {
        return $this->image_manager;
    }

    /**
     * @param ImageManager $image_manager
     */
    public function setImageManager(ImageManager $image_manager)
    {
        $this->image_manager = $image_manager;
    }

    /**
     * @return string
     */
    public function getShape()
    {
        return $this->shape;
    }

    /**
     * @param string $shape
     */
    public function setShape($shape)
    {
        $this->shape = $shape;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param int $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * @param $char
     * @param string $encoding
     * @return null
     */
    static public function order($char, $encoding = "UTF-8")
    {
        $char = mb_convert_encoding($char, "UCS-4BE", $encoding);
        $order = unpack("N", $char);
        return ($order ? $order[1] : null);
    }
    
    /**
     * @return \Intervention\Image\Image
     */
    public function generate()
    {
        $words = $this->break_words($this->name);

        $number_of_word = 1;
        foreach ($words as $word) {

            if ($number_of_word > 2)
                break;

            $this->name_initials .= mb_strtoupper(trim(mb_substr ($word, 0, 1,'UTF-8')));

            $number_of_word++;
        }

        $colors = [
            "#1abc9c", "#2ecc71", "#3498db", "#9b59b6", "#34495e", "#16a085", "#27ae60", "#2980b9", "#8e44ad", "#2c3e50",
            "#f1c40f", "#e67e22", "#e74c3c", "#ecf0f1", "#95a5a6", "#f39c12", "#d35400", "#c0392b", "#bdc3c7", "#7f8c8d",
            "#ef5350", "#ec407a", "#ab47bc", "#7e57c2", "#5c6bc0", "#42a5f5", "#29b6f6", "#26c6da", "#26a69a", "#66bb6a",
            "#9ccc65", "#d4e157", "#ffca28", "#ffa726", "#ff7043", "#8d6e63", "#bdbdbd", "#78909c", "#43a047", "#0288d1",
        ];

        $char_index  = $this->order($this->name_initials) - 44;
        $color_index = $char_index % 20;
        $color       = $colors[$color_index];


        if ($this->shape == 'circle') {
            $canvas = $this->image_manager->canvas(480, 480);

            $canvas->circle(480, 240, 240, function ($draw) use ($color) {
                $draw->background($color);
            });

        } else {

            $canvas = $this->image_manager->canvas(480, 480, $color);
        }

        $canvas->text($this->name_initials, 240, 240, function ($font) {
            $font->file(__DIR__ . '/fonts/arial-bold.ttf');
            $font->size(220);
            $font->color('#ffffff');
            $font->valign('middle');
            $font->align('center');
        });

        return $canvas->resize($this->size, $this->size);
    }

    public function __toString()
    {
        return (string) $this->generate()->encode('data-url');
    }

    public function break_words($name) {
        $temp_word_arr = explode(' ', $name);
        $final_word_arr = array();
        foreach ($temp_word_arr as $key => $word) {
            if( $word != "" && $word != ",") {
                $final_word_arr[] = $word;
            }
        }
        return $final_word_arr;
    }

}
