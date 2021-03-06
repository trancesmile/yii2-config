<?php

namespace bupy7\config\components;

use Yii;
use bupy7\config\models\Config;
use bupy7\config\Module;
use Exception;

/**
 * Management of configuration parameters. You can get parameters via this component.
 * Example:
 * ~~~
 * Yii::$app->configManager->get('example', 'example');
 * ~~~
 * 
 * @author Vasilij "BuPy7" Belosludcev http://mihaly4.ru
 * @since 1.0.0
 */
class ConfigManager extends \yii\base\Component
{
    /**
     * @var array List of configuration parameters of application.
     */
    private $_params;
    
    /**
     * Clear cache with parameters of config the application.
     * @return boolean
     */
    public function clearCache()
    {
        $module = Module::getInstance(); 
        if ($module->cache->exists([__CLASS__, 'params'])) {
            return $module->cache->delete([__CLASS__, 'params']);
        }
        return true;
    }
    
    /**
     * Getting value of parameter from $_params by group and name. 
     * If such parameter undefined, will throw an exception. 
     * 
     * @param string $module Name of module.
     * @param string $name Name of parameter.
     * @return mixed
     */
    public function get($module, $name)
    {
        $this->prepare();
        if (isset($this->_params[$module][$name])) {
            $param = $this->_params[$module][$name];
            if (isset($param[Yii::$app->language])) {
                return $param[Yii::$app->language];
            } elseif (isset($param[Module::LANGUAGE_ALL])) {
                return $param[Module::LANGUAGE_ALL];
            }
        }
        throw new Exception(Module::t('PARAMETER_NOT_FOUND', ['module' => $module, 'name' => $name]), 500);
    }
    
    /**
     * Preparing parameters of config the application.
     */
    protected function prepare()
    {
        if (!isset($this->_params)) {
            $module = Module::getInstance();     
            if ($module->enableCaching) {
                if (($this->_params = $module->cache->get([__CLASS__, 'params'])) === false) {
                    $this->_params = Config::paramsArray();
                    $module->cache->set([__CLASS__, 'params'], $this->_params);
                }
            } else {
                $this->_params = Config::paramsArray();
            }
        }
    }
    
}
