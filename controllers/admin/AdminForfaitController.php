<?php
require_once _PS_MODULE_DIR_ . '/ps_forfait_suivi/classes/Forfaits.php';

class AdminForfaitController extends ModuleAdminController
{
    /*
     * Instanciation of the class
     * Define basic settings
     */
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = Forfaits::$definition['table'];
        $this->identifier = Forfaits::$definition['primary'];
        $this->className = Forfaits::class;
        $this->lang = true;

        parent::__construct();

        $this->fields_list = [
            'id_psforfait' => [
                'title' => $this->module->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ],
            'title' => [
                'title' => $this->module->l('Titre du forfait'),
                'lang' => true,
                'align' => 'left',
            ],
            'total_time' => [
                'title' => $this->module->l('Temps total du forfait (HH:mm)'),
                'align' => 'center',
                'callback' => 'displayTotalTime',
                'callback_object' => 'Forfaits',
                'search' => false,
            ],
            'description' => [
                'title' => $this->module->l('Description du forfait'),
                'lang' => true,
                'align' => 'left',
            ],
            'created_at' => [
                'title' => $this->module->l('Date de création'),
                'align' => 'left',
            ],
            'updated_at' => [
                'title' => $this->module->l('Date de modification'),
                'align' => 'left',
            ]
        ];

        // Add actions on each lines
        $this->addRowAction('edit');
        $this->addRowAction('delete');
    }

    public function getFormValues()
    {
        $fields_list;
        $idShop = $this->context->shop->id;
        $idInfo = Forfaits::getForfaitsByShop($idShop);

        Shop::setContext(Shop::CONTEXT_SHOP, $idShop);
        $info = new Forfaits((int) $idInfo);

        $fields_list['title'] = $info->text;
        $fields_list['description'] = $idInfo;

        return $fields_list;
    }

    public function renderList()
    {
        $forfaits = Db::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'forfaits`');

        foreach ($forfaits as $forfait) {
            $totalTime = (int)$forfait['total_time'];
            $remainingTime = Forfaits::convertSecondsToTime($totalTime);
            if ($totalTime === 0) {
                $this->errors[] = $this->l('Le forfait ' . $forfait['id_psforfait'] . ' est épuisé ! Temps restant : ') . '00:00';
            } else {
                $this->confirmations[] = $this->l('Le temps disponible sur le forfait est de ') . $remainingTime;
            }
        }

        return parent::renderList();
    }

    public function renderForm()
    {
        $this->addJS(_MODULE_DIR_ . 'ps_forfait_suivi/views/js/task-time-input.js');
        $submitName = "addForfait";

        if (Tools::isSubmit("addforfaits")) {
            $submitName = "addForfait";
        }
        if (Tools::isSubmit("updateforfaits")) {
            $submitName = "editForfait";
        }

        $this->fields_form = [
            // Head
            'legend' => [
                'title' => $this->module->l('Éditer un forfait'),
                'icon' => 'icon-cog',
                'method' => 'post',
            ],
            // Fields
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->module->l('Titre'),
                    'name' => 'title',
                    'class' => 'forfait-title',
                    'size' => 255,
                    'required' => true,
                    'empty_message' => $this->module->l('Renseignez le titre du forfait'),
                    'lang' => true,
                    'hint' => $this->module->l('Renseignez le titre du forfait')
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Temps total du forfait (HH:mm)'),
                    'name' => 'total_time',
                    'required' => true,
                    'desc' => $this->l('Entrez le temps au format HH:mm.'),
                    'hint' => $this->l('Le temps sera converti en secondes pour les calculs.'),
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->module->l('Description du forfait'),
                    'name' => 'description',
                    'class' => 'forfait-desc',
                    'required' => true,
                    'empty_message' => $this->module->l('Renseignez la description du forfait'),
                    'lang' => true,
                    'rows' => 10,
                    'cols' => 100,
                    'autoload_rte' => true
                ],
                [
                    'type' => 'hidden',
                    'name' => 'created_at',
                    'id' => 'created_at',
                    'required' => false,
                ],
                [
                    'type' => 'hidden',
                    'name' => 'updated_at',
                    'id' => 'updated_at',
                    'required' => false,
                ]
            ],
            // Submit button
            'submit' => [
                'title' => $this->l('Save'),
                'name' => $submitName,
            ]
        ];

        if ($this->object->id) {
            $this->object->total_time = Forfaits::convertSecondsToTime($this->object->total_time);
        }

        return parent::renderForm();
    }

    public function postProcess()
    {
        if (Tools::isSubmit("addForfait")) {
            $total_time = Tools::getValue('total_time');

            if (!Tasks::isTimeFormat($total_time)) {
                $this->errors[] = $this->l('Le format du temps doit être de type HH:mm (ex: 02:30, 30:00, 100:15).');
                return false;
            }

            $_POST['total_time'] = Forfaits::convertTimeToSeconds($total_time);
            $this->submitAddForfait();
        }

        if (Tools::isSubmit("editForfait")) {
            $total_time = Tools::getValue('total_time');

            if (!Tasks::isTimeFormat($total_time)) {
                $this->errors[] = $this->l('Le format du temps doit être de type HH:mm (ex: 02:30, 30:00, 100:15).');
                return false;
            }

            $_POST['total_time'] = Forfaits::convertTimeToSeconds($total_time);
            $this->submitEditForfaits();
        }

        if (Tools::isSubmit('deleteforfaits')) {
            $id_forfait = (int)$_GET['id_psforfait'];
            $taskCount = (int)Db::getInstance()->getValue('SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'tasks` WHERE `id_psforfait` = ' . $id_forfait);

            if ($taskCount > 0) {
                echo '<div class="alert alert-warning">Ce forfait contient des tâches associées. Êtes-vous sûr de vouloir supprimer ce forfait et toutes ses tâches ?</div>';
            } else {
                Db::getInstance()->delete(Forfaits::$definition['table'], 'id_psforfait = ' . $id_forfait);
                Db::getInstance()->delete(Forfaits::$definition['table'] . '_lang', 'id_psforfait = ' . $id_forfait);
            }
        }
    }

    public function submitAddForfait()
    {
        $created_at = date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');

        Db::getInstance()->insert(Forfaits::$definition['table'], array(
            'total_time' => (int)$_POST['total_time'],
            'created_at' => $created_at,
            'updated_at' => $updated_at,
        )
        );

        $id_forfait = (int)Db::getInstance()->Insert_ID();
        $languages = Language::getLanguages();
        foreach ($languages as $lang) {
            $language = (int) $lang['id_lang'];
            Db::getInstance()->insert(Forfaits::$definition['table'] . "_lang", array(
                'id_psforfait' => $id_forfait,
                'id_lang' => $language,
                'title' => pSQL($_POST['title_' . $language]),
                'description' => pSQL($_POST['description_' . $language], true),
            ));
        }
    }

    public function submitEditForfaits()
    {
        $updated_at = date('Y-m-d H:i:s');
        $id_psforfait = (int)$_POST['id_psforfait'];
        $newTotalTime = (int)$_POST['total_time'];

        $oldTotalTime = (int)Db::getInstance()->getValue('SELECT `total_time` FROM `' . _DB_PREFIX_ . 'forfaits` WHERE `id_psforfait` = ' . $id_psforfait);

        Db::getInstance()->update(Forfaits::$definition['table'], array(
            'total_time' => $newTotalTime,
            'updated_at' => $updated_at,
        ), 'id_psforfait = ' . $id_psforfait);

        $languages = Language::getLanguages();
        foreach ($languages as $lang) {
            $language = (int) $lang['id_lang'];
            Db::getInstance()->update(Forfaits::$definition['table'] . '_lang', array(
                'title' => pSQL($_POST['title_' . $language]),
                'description' => pSQL($_POST['description_' . $language], true),
            ), 'id_psforfait = ' . $id_psforfait);
        }

        if ($oldTotalTime != $newTotalTime) {
            Db::getInstance()->update(Tasks::$definition['table'], array(
                'current' => 0
            ), 'id_psforfait = ' . $id_psforfait . ' AND current = 1');

            $this->confirmations[] = $this->l('Le total_time a été modifié. Toutes les tâches associées à ce forfait ont été déattribuées.');
        }
    }

    public function initPageHeaderToolbar()
    {
        $forfaitCount = Db::getInstance()->getValue('SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'forfaits`');

        if ($forfaitCount == 0) {
            $this->page_header_toolbar_btn['Nouveau'] = array(
                'href' => self::$currentIndex . '&add' . $this->table . '&token=' . $this->token,
                'desc' => $this->module->l('Ajout nouveau forfait'),
                'icon' => 'process-icon-new'
            );
        }

        parent::initPageHeaderToolbar();
    }
    
    public function getForfaitTitle($id_forfait)
    {
        $id_lang = (int)Context::getContext()->language->id;
        $query = 'SELECT `title` FROM `' . _DB_PREFIX_ . 'forfaits_lang` WHERE `id_psforfait` = ' . (int)$id_forfait . ' AND `id_lang` = ' . $id_lang;
        $title = Db::getInstance()->getValue($query);
        return $title ? $title : 'Sans titre';
    }

     public function getForfaitTime($id_forfait)
     {
         $sql = 'SELECT `total_time` FROM `' . _DB_PREFIX_ . 'forfaits` WHERE `id_psforfait` = ' . (int)$id_forfait;
         $total_time = (int)Db::getInstance()->getValue($sql);
         return $total_time;
     }
}