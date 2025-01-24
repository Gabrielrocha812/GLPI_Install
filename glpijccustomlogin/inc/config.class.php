<?php

class PluginGlpijccustomloginConfig extends CommonDBTM
{
    private static $CACHE = null;

    const FILES_PLUGIN_DIR = (GLPI_PLUGIN_DOC_DIR . DIRECTORY_SEPARATOR . "glpijccustomlogin");

    const FILES_NAMES = [
        'logo',
        'background'
    ];

    function getTabNameForItem(CommonGLPI $item, $withtemplate = 0)
    {
        switch ($item::getType()) {
            case 'Config':
                return __('JC Custom Login', 'glpijccustomlogin');
                break;
        }
        return '';
    }

    static function displayTabContentForItem(CommonGLPI $item, $tabnum = 1, $withtemplate = 0)
    {
        switch ($item::getType()) {
            case 'Config':
                $config = new self();
                $config->show();
                break;
        }
    }

    static function getConfig($name, $defaultValue = null) {
        if (self::$CACHE === null) {
            $config = new self();
            $config->getEmpty();
            $config->fields = array_merge($config->fields, Config::getConfigurationValues('glpijccustomlogin'));

            self::$CACHE = $config->fields;
        }

        if (isset(self::$CACHE[$name]) && self::$CACHE[$name] !== '') {
            return self::$CACHE[$name];
        }

        return $defaultValue;
    }

    public function show()
    {
        if (!Config::canView()) {
            return false;
        }

        $fields = [
            "logo" => "",
            "background" => ""
        ];

        $fields = array_merge($fields, Config::getConfigurationValues('glpijccustomlogin'));

        echo "
            <style>
                #glpijccustomlogin_tbody td {
                    padding-top: 10px;
                }
            </style>
        ";

        echo "<form name='form' action=\"" . Toolbox::getItemTypeFormURL('Config') . "\" method='post'>";
        echo Html::hidden('config_context', ['value' => 'glpijccustomlogin']);
        echo Html::hidden('config_class', ['value' => __CLASS__]);

        echo "<div class=\"center\">";
        echo "<table class=\"tab_cadre_fixe\">";
        echo "<tbody id=\"glpijccustomlogin_tbody\">";
        echo "<tr><th colspan=\"4\" >Personalização</th></tr>";

        echo "<tr><td></td></tr>";

        echo "<tr class=\"tab_bg_1\"><td>Logo</td>";
        if (!empty($fields['logo'])) {
            echo '<td style="text-align: center;">';
            echo Html::image(self::getImageUrl($fields['logo']), [
                'style' => '
                    max-width: 100px;
                    max-height: 100px;
                    background-image: linear-gradient(45deg, #b0b0b0 25%, transparent 25%), linear-gradient(-45deg, #b0b0b0 25%, transparent 25%), linear-gradient(45deg, transparent 75%, #b0b0b0 75%), linear-gradient(-45deg, transparent 75%, #b0b0b0 75%);
                    background-size: 10px 10px;
                    background-position: 0 0, 0 5px, 5px -5px, -5px 0px;',
                'class' => 'picture_square'
            ]);
            echo '</td>';
        } else if (!empty($fields['background'])) {
            echo '<td></td>';
        }

        echo "<td>";

        Html::file([
            'name'       => "logo",
            'onlyimages' => true,
        ]);
        echo "</td></tr>";

        echo "<tr class=\"tab_bg_1\"><td>Background</td>";
        if (!empty($fields['background'])) {
            echo '<td style="text-align: center;">';
            echo Html::image(self::getImageUrl($fields['background']), [
                'style' => '
                    max-width: 100px;
                    max-height: 100px;
                    background-image: linear-gradient(45deg, #b0b0b0 25%, transparent 25%), linear-gradient(-45deg, #b0b0b0 25%, transparent 25%), linear-gradient(45deg, transparent 75%, #b0b0b0 75%), linear-gradient(-45deg, transparent 75%, #b0b0b0 75%);
                    background-size: 10px 10px;
                    background-position: 0 0, 0 5px, 5px -5px, -5px 0px;',
                'class' => 'picture_square'
            ]);
            echo '</td>';
        } else if (!empty($fields['logo'])) {
            echo '<td></td>';
        }

        echo "<td>";

        Html::file([
            'name'       => "background",
            'onlyimages' => true,
        ]);
        echo "</td></tr>";

        echo "<tr><td></td></tr>";

        echo "<tr class='tab_bg_1'><td class='center' style=\"text-align: center;\" colspan='4'>";
        echo "<input type='submit' name='update' class='submit' value=\"" . __s('Salvar') . "\" >";
        echo "</td></tr>\n";

        echo "</tbody>";
        echo "</table>";
        echo "</div>";

        Html::closeForm();
    }

    static function configUpdate($input)
    {
        $old = Config::getConfigurationValues('glpijccustomlogin');

        $input['logo'] = empty($input['_logo']) ? null : $input['_logo'][array_key_last($input['_logo'])];
        $input['background'] = empty($input['_background']) ? null : $input['_background'][array_key_last($input['_background'])];

        $input = self::checkPicture('logo', 'logo', $input, $old, 145, 80, 300);
        $input = self::checkPicture('background', 'background', $input, $old);

        unset($input['_logo']);
        unset($input['_prefix_logo']);
        unset($input['_tag_logo']);
        unset($input['_uploader_logo']);
        
        unset($input['_background']);
        unset($input['_prefix_background']);
        unset($input['_tag_background']);
        unset($input['_uploader_background']);

        return $input;
    }

    static function checkPicture($name, $filename, $input, $old, $width = 0, $height = 0, $max_size = 500)
    {
        if (empty($input[$name])) return $input;

        $tempImg = $input[$name];
        $imgPath = GLPI_TMP_DIR . '/' . $tempImg;
        $imgResizedPath = GLPI_TMP_DIR . '/resized_' . $tempImg;

        if ($width || $height) {
            if (Toolbox::resizePicture($imgPath, $imgResizedPath, $width, $height, 0, 0, 0, 0, $max_size)) {
                $imgPath = $imgResizedPath;
            }
        }

        if ($dest = self::savePicture($imgPath, $filename, $old, $name)) {
            $input[$name] = $dest;
        } else {
            Session::addMessageAfterRedirect(__('Não foi possível salvar a imagem.'), true, ERROR);
        }

        return $input;
    }

    static public function savePicture($src, $filename, $old, $name)
    {
        $imgPluginPath = self::FILES_PLUGIN_DIR;

        if (function_exists('Document::isImage') && !Document::isImage($src)) {
            return false;
        } else if (function_exists('Document::isPicture') && !Document::isPicture($src)) {
            return false;
        }

        if (!empty($old[$name])) {
            $destOld = $imgPluginPath . DIRECTORY_SEPARATOR . $old[$name];
            if (file_exists($destOld)) {
                if (!@unlink($destOld)) return false;
            }
        }

        $ext = pathinfo($src, PATHINFO_EXTENSION);
        $dest = $imgPluginPath . DIRECTORY_SEPARATOR . $filename . uniqid() . '.' . $ext;

        if (!is_dir($imgPluginPath) && !mkdir($imgPluginPath, 0777, true)) {
            return false;
        }

        if (!rename($src, $dest)) {
            return false;
        }

        return substr($dest, strlen($imgPluginPath . '/')); // Return dest relative to GLPI_PICTURE_DIR
    }

    static public function startsWith($haystack, $needle)
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    static function getImageUrl($imgPath)
    {
        $imgPath = Html::cleanInputText($imgPath); // prevent xss

        if (empty($imgPath)) {
            return null;
        }

        return Html::getPrefixedUrl('/plugins/glpijccustomlogin/front/config.form.php?img_path=' . $imgPath);
    }
}
