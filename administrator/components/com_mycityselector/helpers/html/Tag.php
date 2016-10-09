<?php
/**
 * Html Tag Constructor
 *
 * @author Konstantin Kutsevalov
 * @email <mail@art-prog.ru>
 */
namespace adamasantares\html;

if (function_exists('\adamasantares\html\tg') || class_exists('\adamasantares\html\Tag')) return;

class Tag {
    /**
     * @var integer
     */
    private $level = 0;
    /**
     * Single tags
     * @var array
     */
    private static $single = ['area', 'base', 'basefont', 'bgsound', 'br', 'col', 'command', 'embed', 'hr', 'img',
        'input', 'isindex', 'keygen', 'link', 'meta', 'param', 'source', 'track', 'wbr'];
    /**
     * @var string
     */
    private $tagName = 'div';
    /**
     * @var array
     */
    private $contents = [];
    /**
     * @var array
     */
    private $classesNames = [];
    /**
     * @var array
     */
    private $attributes = [];
    /**
     * Creates new tag object
     * @param string|array $properties <p>
     *  This is complex argument. It may describes tag name, classes names, "id", "rel" attribute, "type" attribute
     *      and name of attribute for $content (if a tag is single).
     *  Example 1: "script #jquery !text/javascript %src" or "script#jquery!text/javascript%src"
     *      there are:
     *          "script" - is tag name
     *          "#jquery" - is value for "id" attribute
     *          "!text/javascript" - is value for "type" attribute
     *          "%src" - is name of attribute for $content
     *  Example 2: "link @icon !image/x-icon %href" or "link@icon!image/x-icon%href"
     *      there are:
     *          "link" - is tag name
     *          "@icon" - is value for "rel" attribute
     *          "!image/x-icon" - is value for "type" attribute
     *          "%href" - is name of attribute for $content
     *  Example 3: tg(['a.button', 'href' => '/url/path'], tg(['img.my-img%src', 'alt' => 'just-a-link'], '/image.png'));
     *
     * ```<a class="button" href="/url/path"><img src="/image.png" alt="just-a-link" class="my-img"></a>```
     *
     * Example 4: tg('input!text$option', '123'); // for Input tag no need to define name of attribute for value
     *
     * ```<input type="text" name="option" value="123">```
     *
     * Example 5: tg('.super.puper.element', 'My content');  // by default a tag name is "DIV"
     *
     * ```<div class="super puper element">My content</div>```
     *
     * Tokens: "#id", ".class", "$nameAttributeValue", "@relAttributeValue", "!typeAttributeValue", "%contentAttributeName"
     * </p>
     * @param mixed $content <p>
     *  For single tags (like input, meta, img, etc..) this may be value for special
     *    attribute that defined in $properties argument via "%" char
     *    tg('script@text/javascript%src', '/js/script.js');
     *    in this example the "%src" is the name of attribute that will get $content value
     * </p>
     */
    public function __construct($properties, $content = null)
    {
        // 'tagName.class.class.class#id$nameAttr@rel!type%contentAttribute'
        $idString = '';
        if (is_array($properties)) {
            // parse attributes
            foreach ($properties as $property => $value) {
                if ($property == '0' || (!is_string($value) && !is_numeric($value))) continue;
                if (is_numeric($property)) {
                    $this->attributes[$value] = $value;
                } else {
                    $this->attributes[$property] = $value;
                }
            }
            if (isset($properties[0])) {
                $idString = $properties[0];
            }
        } else {
            $idString = $properties;
        }
        // name, class, id,  etc.
        // Tokens: "#id", ".class", "$nameAttributeValue", "@relAttributeValue", "!typeAttributeValue", "%contentAttributeName"
        $idString = str_replace(['#', '.', '$', '@', '!', '%', '  '], [' #', ' .', ' $', ' @', ' !', ' %', ' '], $idString);
        $idString = explode(' ', trim($idString));
        $idString = array_filter($idString, function($v){
            return !empty($v);
        });
        foreach ($idString as $token) {
            $char = substr($token, 0, 1);
            switch ($char) {
                case '#':
                    $this->id(substr($token, 1));
                    break;
                case '.':
                    $this->addCls(substr($token, 1));
                    break;
                case '$':
                    $this->attributes['name'] = substr($token, 1);
                    break;
                case '@':
                    $this->attributes['rel'] = substr($token, 1);
                    break;
                case '!':
                    $this->attributes['type'] = substr($token, 1);
                    break;
                case '%':
                    if (is_string($content) || is_numeric($content)) {
                        $this->attributes[substr($token, 1)] = htmlspecialchars($content);
                        $content = ''; // reset content
                    }
                    break;
                default:
                    $this->tagName = $token;
            }
        }
        // contents
        if (!in_array($this->tagName, self::$single)) {
            // tag pair
            if ($this->tagName == 'select' && is_array($content)) {
                $this->contents = $this->generateSelectOptions($content);
            } else if (($this->tagName == 'ul' || $this->tagName == 'ol') && is_array($content)) {
                foreach ($content as $item) {
                    if (is_string($item) || is_numeric($item)) {
                        $this->contents[] = tg('li', $item);
                    } else {
                        $this->contents[] = $item;
                    }
                }
            } else {
                if (!empty($content)) {
                    if (!is_array($content)) {
                        $content = [$content];
                    }
                    $this->contents = $content;
                }
                if ($this->tagName == 'a' && !isset($this->attributes['title'])) {  // fix "title" of LINK
                    $this->attributes['title'] = '';
                } else if ($this->tagName == 'img' && !isset($this->attributes['alt'])) {  // fix "alt" of IMG
                    $this->attributes['alt'] = '';
                }
            }
        } else {
            // single tag
            if (is_string($content) || is_numeric($content)) {
                if ($this->tagName == 'input') {
                    $this->attributes['value'] = $content;
                } else if ($this->tagName == 'img') {
                    $this->attributes['src'] = $content;
                    if (!isset($this->attributes['alt'])) {  $this->attributes['alt'] = '';  } // fix "alt" of "img"
                } else if ($this->tagName == 'meta') {
                    $this->attributes['content'] = $content;
                } else if ($this->tagName == 'link') {
                    $this->attributes['href'] = $content;
                }
            }
        }
    }
    /**
     * Getter and Setter for tag ID
     * @param string $id
     * @return $this|string If used as setter then will be returned current Tag object
     */
    public function id($id = null)
    {
        if ($id === null) {
            return $this->attr('id');
        }
        $this->attr('id', $id);
        return $this;
    }
    /**
     * Getter and Setter for tag attributes
     * @param $name
     * @param string $value
     * @return $this|string If used as setter then will be returned current Tag object
     */
    public function attr($name, $value = null)
    {
        // getter
        if ($value === null) {
            return isset($this->attributes[$name]) ? $this->attributes[$name] : '';
        }
        // setter
        if (is_object($value)) {
            $value = @get_class($value);
        } else if (is_array($value)) {
            $value = array_filter($value, function($v) {
                return (is_string($v) || is_numeric($v)) ? true : false;
            });
            if ($name != 'class') {
                $value = implode(' ', $value);
            }
        }
        if ($name == 'class') {
            $this->addCls($value);
        } else {
            $this->attributes[$name] = $value;
        }
        return $this;
    }
    /**
     * Getter and Setter for tag name
     * @param string $name
     * @return $this|string If used as setter then will be returned current Tag object
     */
    public function name($name = null)
    {
        // getter
        if ($name === null) {
            return $this->tagName;
        }
        // setter
        if (is_string($name)) {
            $this->tagName = $name;
        }
        return $this;
    }
    /**
     * Add class name to tag
     * @param string|array $name
     * @return $this
     */
    public function addCls($name)
    {
        if (is_string($name)) {
            $name = explode(' ', trim($name));
        }
        if (is_array($name)) {
            foreach ($name as $value) {
                if (!in_array($value, $this->classesNames)) {
                    $this->classesNames[] = $value;
                }
            }
        }
        return $this;
    }
    /**
     * Deletes class name of tag
     * @param string|array $name
     * @return $this
     */
    public function delCls($name)
    {
        if (is_string($name)) {
            $name = explode(' ', trim($name));
        }
        if (is_array($name)) {
            foreach ($name as $value) {
                $key = array_search($value, $this->classesNames);
                if ($key !== false) {
                    unset($this->classesNames[$key]);
                }
            }
        }
        return $this;
    }
    /**
     * Checks class name of tag
     * @param string $name
     * @return boolean
     */
    public function isCls($name)
    {
        return array_search($name, $this->classesNames) === false ? false : true;
    }
    /**
     * Appends content
     * @param $content
     * @return $this
     */
    public function append($content)
    {
        if ((is_object($content) && get_class($content) == get_class($this)) || is_string($content) || is_numeric($content)) {
            $this->contents[] = $content;
        }
        return $this;
    }
    /**
     * Clears all content
     * @return $this
     */
    public function clear()
    {
        $this->contents = [];
        return $this;
    }
    /**
     * @param $options <p>
     *  [
     *      '1' => 'Cat',
     *      '20' => ['Dog', 'selected'],
     *      '58' => ['Hamster', 'disabled']
     *  ]
     * </p>
     * @return array
     */
    private function generateSelectOptions($options = [])
    {
        $tags = [];
        if (is_array($options)) {
            foreach ($options as $value => $properties) {
                // properties may be string (title) or array of [title, selected]
                if (!is_array($properties)) {
                    $properties = [$properties];
                }
                $id = ['option', 'value' => $value];
                $selectedKey = array_search('selected', $properties);
                $disabledKey = array_search('disabled', $properties);
                if ($selectedKey !== false) {
                    unset($properties[$selectedKey]);
                    $properties = array_values($properties);
                    $id['selected'] = 'selected';
                }
                if ($disabledKey !== false) {
                    unset($properties[$disabledKey]);
                    $properties = array_values($properties);
                    $id['disabled'] = 'disabled';
                }
                $tags[] = new self($id, $properties[0]);
            }
        }
        return $tags;
    }
    /**
     * Set element's level
     * @param $level
     * @return $this
     */
    public function setLevel($level)
    {
        $this->level = $level;
        return $this;
    }
    /**
     * Search elements
     * @param $query
     */
    public function query($query)
    {
        // TODO query
    }
    public function __toString()
    {
        $offset = str_repeat("\t", $this->level);
        $tag = '';
        $this->tagName = strtolower($this->tagName);
        if ($this->tagName == 'html5') {
            $tag .= "<!DOCTYPE html>\n";
            $this->tagName = 'html';
        }
        $tag .= $offset . '<' . $this->tagName;
        // attributes
        foreach ($this->attributes as $attr => $value) {
            $tag .= ' ' . $attr . '="' . htmlspecialchars($value) . '"';
        }
        // classes
        if (!empty($this->classesNames)) {
            $tag .= ' class="';
            foreach ($this->classesNames as $i => $cls) {
                $tag .= ($i == 0 ? '' : ' ') . $cls;
            }
            $tag .= '"';
        }
        $tag .= '>';
        if (!in_array($this->tagName, self::$single)) {
            // if tag pair
            foreach ($this->contents as $content) {
                if (!empty($content)) {
                    if (is_string($content) || is_numeric($content)) {
                        if ($content == 'br/' || $content == 'hr/') { // special marks for short tags
                            $content = '<' . substr($content, 0, 2) . '>';
                        }
                        $tag .= $content;
                    } else if (is_object($content) && get_class($content) == get_class($this)) {
                        $tag .= "\n";
                        $content->setLevel($this->level + 1);
                        $tag .= $content->__toString();
                    }
                }
            }
            $tag .= "\n" . $offset . '</' . $this->tagName . '>';
        }
        return $tag;
    }
}
/**
 * Alias for create tag object
 * @param string|array $properties
 * @param mixed $content It can be a String, Tag or Tag[]
 * @return Tag
 */
function tg($properties, $content = null)
{
    return new Tag($properties, $content);
}