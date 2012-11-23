<?php

abstract class SDatabaseQueryAbstract extends KObject
{
    /**
     * The builder object that will be used by the query
     * @var SDatabaseQueryBuilderAbstract
     */
    protected $_builder;

    protected $_compiler;

    protected $_schema;

    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        if (!($config->schema instanceof SDatabaseSchemaInterface)) {
            throw new KDatabaseException('Query builder requires a schema');
        }

        $this->_builder = $this->getIdentifier()->name;
        $this->_schema = $config->schema;

    }

    public function getBuilder()
    {
        if(!$this->_builder instanceof SDatabaseQueryBuilderAbstract)
        {
            //Make sure we have a view identifier
            if(!($this->_builder instanceof KServiceIdentifier)) {
                $this->setBuilder($this->_builder);
            }

            $this->_builder = $this->getService($this->_builder, array('schema' => $this->_schema));
        }

        return $this->_builder;
    }

    public function setBuilder($builder)
    {
        if(!($builder instanceof SDatabaseQueryBuilderAbstract))
        {
            if(is_string($builder) && strpos($builder, '.') === false )
            {
                $identifier         = clone $this->getIdentifier();
                $identifier->path   = array('query', 'builder');
                $identifier->name   = $builder;
            }
            else $identifier = $this->getIdentifier($builder);

            if($identifier->path[1] != 'builder') {
                throw new KDatabaseException('Identifier: '.$identifier.' is not a builder identifier');
            }

            $builder = $identifier;
        }

        $this->_builder = $builder;

        return $this->_builder;
    }

    public function getCompiler()
    {
        if ($this->_compiler === null)
        {
            $identifier = clone $this->getBuilder()->getIdentifier();
            $identifier->path = array('query', 'compiler');
            $this->_compiler = $this->getService($identifier);
        }

        return $this->_compiler;
    }

    public function compile($context = null)
    {
        if($context !== null)
        {
            $object       = $this->getBuilder()->getContext($context);
            $object->type = $context;
            $context      = $object;
        }

        return $this->getCompiler()->compile($context);
    }

    public function cloned()
    {
        $clone = clone $this;
        return $clone->reset();
    }

    public function reset()
    {
        $this->_builder = $this->getBuilder()->getIdentifier()->name;
        return $this;
    }

    public function __call($method, array $arguments)
    {
    	$this->getBuilder()->execute($method, $arguments);
    	return $this;
    }

    public function __get($name)
    {
        $this->getBuilder()->execute($name);
        return $this;
    }
}
