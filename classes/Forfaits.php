<?php

class Forfaits extends ObjectModel
{
    /**
     * @var array
     */
    private $loadedData = [];
    public $id_psforfait;
    public $title;
    public $total_time;
    public $description;
    public $created_at;
    public $updated_at;

    public static $definition = [
        'table'     =>  'forfaits',
        'primary'   =>  'id_psforfait',
        'multilang' =>  true,
        'fields'    =>  [
            // Standard fields
            'total_time'    => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true],
            'created_at'     =>  ['type' => self::TYPE_DATE, 'validate' => 'isPhpDateFormat', 'required' => false],
            'updated_at'     =>  ['type' => self::TYPE_DATE, 'validate' => 'isPhpDateFormat', 'required' => false],
            // Lang fields
            'title'     =>  ['type' => self::TYPE_STRING, 'validate' => 'isName', 'size' => 255, 'required' => true],
            'description'     =>  ['type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml', 'required' => true],
        ],
    ];

    public static function displayTotalTime($total_time)
    {
        return self::convertSecondsToTime($total_time);
    }

    public static function getForfaitTitle($id_psforfait, $id_lang = null)
    {
        if (is_null($id_lang)) {
            $id_lang = (int)Context::getContext()->language->id;
        }

        $sql = 'SELECT `title`
            FROM `' . _DB_PREFIX_ . 'forfaits_lang`
            WHERE `id_psforfait` = ' . (int)$id_psforfait . ' AND `id_lang` = ' . (int)$id_lang;

        $title = Db::getInstance()->getValue($sql);

        return $title ? $title : 'Sans titre';
    }


    public static function convertTimeToSeconds($time)
    {
        list($hours, $minutes) = explode(':', $time);
        return $hours * 3600 + $minutes * 60;
    }

    public static function convertSecondsToTime($seconds)
    {
        // Utilisation de la méthode de Tasks pour éviter la duplication
        require_once _PS_MODULE_DIR_ . '/ps_forfait_suivi/classes/Tasks.php';
        return Tasks::convertSecondsToTime($seconds);
    }
}