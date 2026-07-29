<?php

namespace App\Core;
use ReflectionClass;

class Container
{
    private static ?Container $instance = null;

    private array $instances = [];
    private array $resolvers = [];
    private array $singletons = [];

// ├── bind()
// ├── singleton()
// ├── instance()
// ├── make()
// ├── get()
// ├── has()
// └── resolve()
    private function __construct(array $instances = [], array $resolvers = [], array $singletons = [])
    {
        $this->instances = $instances;
        $this->resolvers = $resolvers;
        $this->singletons = $singletons;
    }
    private function __clone() {}
    public function __wakeup() {}

    public static function getInstance():Container
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function bind(string $key, callable $resolver)
    {
        return $this->resolvers[$key] = $resolver;
        
    }

    public function build($key){
        $reflection = new ReflectionClass($key);
        $constructor=$reflection->getConstructor();
        if(!$constructor){
            return $reflection->newInstance();
        }
        $dependencies = [];
        if($constructor){
            $parameters=$constructor->getParameters();
            foreach($parameters as $parameter){
                $type=$parameter->getType();
                if($type){
                    $dependencies[] = $this->make($type->getName());
                }
            }
            return $reflection->newInstanceArgs($dependencies);
        }
    }


    public function resolve(string $key)
    {
        if (isset($this->instances[$key])) {
            return $this->instances[$key];
        }
        if (isset($this->resolvers[$key])) {
            if(isset($this->singletons[$key])){
                $this->instances[$key] = $this->resolvers[$key]();
                return $this->instances[$key];
            }
            return $this->resolvers[$key]();
        }else{
            return $this->build($key);
        }
    }

    public function make(string $key)
    {
        return $this->resolve($key);
    }


    public function has(string $key): bool
    {
        return isset($this->instances[$key]) || isset($this->resolvers[$key]);
    }

    public function get(string $key)
    { 
        if (isset($this->instances[$key])) {
            return $this->instances[$key];
        }
        if (isset($this->resolvers[$key])) {

            return $this->make($key);
        }
        throw new \Exception("{$key} not found in container");
        
    }


    public function singleton(string $key, callable $resolver)
    {
        $this->bind($key, $resolver);
        $this->singletons[$key] = true;
    }

    public function instance(string $key, $instance)
    {
        $this->instances[$key] = $instance;
    }
}