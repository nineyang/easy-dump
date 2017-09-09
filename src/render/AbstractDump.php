<?php
/**
 * User: Nine
 * Date: 2017/9/4
 * Time: 下午9:24
 */

namespace dd\render;

/**
 * Class AbstractDump
 * @package dd\render
 */
abstract class AbstractDump
{
    /**
     * @var
     */
    public $value;

    /**
     * @return "["
     * @var
     */
    public $_leftBracket;

    /**
     * @return "]"
     * @var
     */
    public $_rightBracket;

    /**
     * @return "▶"
     * @var
     */
    public $_triangle;

    /**
     * @return "▼"
     * @var
     */
    public $_invertedTriangle;

    /**
     * @return "=>"
     * @var
     */
    public $_needle;

    /**
     * DumpString constructor.
     * @param $value
     */
    public function __construct($value)
    {
        $this->value = $value;
        $this->init();
    }

    /**
     * 初始化一些需要展示的内容
     */
    protected function init()
    {
        $this->_leftBracket = $this->returnValue("[", 'span', ['nine-span', 'black-color'], ['withQuota' => false]);
        $this->_rightBracket = $this->returnValue("]", 'span', ['nine-span', 'black-color'], ['withQuota' => false]);
        $this->_triangle = $this->returnValue("▶", 'span', ['nine-span', 'gray-color', 'font-12'], ['withQuota' => false]);
        $this->_invertedTriangle = $this->returnValue("▼", 'span', ['nine-span', 'gray-color'], ['withQuota' => false]);
        $this->_needle = $this->returnValue("=>", 'span', ['nine-span', 'black-color'], ['withQuota' => false]);
    }

    /**
     * @var array
     */
    protected $_decorator = [];

    /**
     * @param $type
     * @param array $classArr
     * @param array $params
     * @param string $value
     * @return mixed
     */
    public function returnValue($value = '', $type = 'span', $classArr = ['nine-span'], $params = ['withQuota' => true])
    {
        return ($this->returnDecorator($type, $classArr, $value, $params))->value;
    }

    /**
     * @param $type
     * @param array $classArr
     * @param array $params
     * @param string $value
     * @return mixed
     */
    protected function returnDecorator($type, $classArr = [], $value = '', $params = [])
    {
        $decorator = $this->{$type}($value ?: $this->value);
        if (!empty($classArr)) {
            foreach ($classArr as $class) {
                $decorator->addClass($class);
            }
        }
//        因为是单例，所以这里的value需要重新修改一下
        $decorator->value = $value;
        return $decorator->addDecorator($params);
    }

    /**
     * @param $value
     * @param null $type
     * @param array $classArr
     * @param array $params
     */
    public function display($value = '', $type = null, $classArr = [], $params = [])
    {
        if (is_array($value)) {
            $value = implode('', $value);
        } elseif ($value == '') {
            $value = $this->value;
        }

//       此时为空代表已经拼接好
        if (is_null($type)) {
            $divDecorator = $this->returnDecorator('div', ['nine-div'], $value);
        } else {
            $typeDecorator = $this->returnDecorator($type, $classArr, $value, $params);

            $divDecorator = $this->returnDecorator('div', ['nine-div'], $typeDecorator->value);
        }

        $divDecorator->display();
        die();
    }

    /**
     * @param $decorator
     * @return \dd\decorator\DecoratorComponent
     */
    public function __get($decorator)
    {
        array_key_exists($decorator, $this->_decorator) ?: $this->_decorator[$decorator] = $this->withObject("dd\\decorator\\" . ucfirst($decorator), $this->value);
        return $this->_decorator[$decorator];
    }

    /**
     * 为方便层层包裹
     * @param $decorator
     * @param $arguments
     * @return \dd\decorator\DecoratorComponent
     */
    public function __call($decorator, $arguments)
    {
        array_key_exists($decorator, $this->_decorator) ?: $this->_decorator[$decorator] = $this->withObject("dd\\decorator\\" . ucfirst($decorator), array_pop($arguments));
        return $this->_decorator[$decorator];
    }

    /**
     * @param $class
     * @param null $arguments
     * @return mixed
     */
    protected function withObject($class, $arguments = null)
    {
        return is_null($arguments) ? new $class : new $class($arguments);
    }

    abstract public function render();
}