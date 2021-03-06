<?php

namespace Biskuit\Filter;

class FilterManager
{
    /**
     * @var array
     */
    protected $defaults = [
        'addrelnofollow' => 'Biskuit\Filter\AddRelNofollowFilter',
        'alnum'          => 'Biskuit\Filter\AlnumFilter',
        'alpha'          => 'Biskuit\Filter\AlphaFilter',
        'bool'           => 'Biskuit\Filter\BooleanFilter',
        'boolean'        => 'Biskuit\Filter\BooleanFilter',
        'digits'         => 'Biskuit\Filter\DigitsFilter',
        'int'            => 'Biskuit\Filter\IntFilter',
        'integer'        => 'Biskuit\Filter\IntFilter',
        'float'          => 'Biskuit\Filter\FloatFilter',
        'json'           => 'Biskuit\Filter\JsonFilter',
        'pregreplace'    => 'Biskuit\Filter\PregReplaceFilter',
        'slugify'        => 'Biskuit\Filter\SlugifyFilter',
        'string'         => 'Biskuit\Filter\StringFilter',
        'stripnewlines'  => 'Biskuit\Filter\StripNewlinesFilter'
    ];

    /**
     * @var FilterInterface[]
     */
    protected $filters = [];

    /**
     * Constructor.
     *
     * @param array $defaults
     */
    public function __construct(array $defaults = null) {
        if (null !== $defaults) {
            $this->defaults = $defaults;
        }
    }

    /**
     * Apply shortcut.
     *
     * @see apply()
     */
    public function __invoke($value, $name, array $options = [])
    {
        return $this->apply($value, $name, $options);
    }

    /**
     * Apply a filter.
     *
     * @param  mixed  $value
     * @param  string $name
     * @param  array  $options
     * @return FilterInterface The filter
     * @throws \InvalidArgumentException
     */
    public function apply($value, $name, array $options = [])
    {
        return $this->get($name, $options)->filter($value);
    }

    /**
     * Gets a filter.
     *
     * @param  string $name
     * @param  array  $options
     * @return FilterInterface The filter
     * @throws \InvalidArgumentException
     */
    public function get($name, array $options = [])
    {
        if (array_key_exists($name, $this->defaults)) {
            $this->filters[$name] = $this->defaults[$name];
        }

        if (!array_key_exists($name, $this->filters)) {
            throw new \InvalidArgumentException(sprintf('Filter "%s" is not defined.', $name));
        }

        if (is_string($class = $this->filters[$name])) {
            $this->filters[$name] = new $class;
        }

        $filter = clone $this->filters[$name];
        $filter->setOptions($options);

        return $filter;
    }

    /**
     * Registers a filter.
     *
     * @param string $name
     * @param string|FilterInterface $filter
     * @throws \InvalidArgumentException
     */
    public function register($name, $filter)
    {
        if (array_key_exists($name, $this->filters)) {
            throw new \InvalidArgumentException(sprintf('Filter with the name "%s" is already defined.', $name));
        }

        if (is_string($filter) && !class_exists($filter)) {
            throw new \InvalidArgumentException(sprintf('Unknown filter with the class name "%s".', $filter));
        }

        $this->filters[$name] = $filter;
    }
}
