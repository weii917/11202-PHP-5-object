<?php

class Animal{
    protected $name;

    public function __construct($name)
    {
        $this->name=$name;
    }

    public function setName($name){
        $this->name=$name;
    }

    public function getName(){
        return $this->name;
    }

}

$animal=new Animal('阿明'); //實例化 instant

echo '顯示名稱:'.$animal->getName();
echo "<br>";
$animal->setName('小花');
echo '顯示名稱:'.$animal->getName();
echo "<br>";
/* $animal->name='阿中';
echo '顯示名稱:'.$animal->name; */
echo "<br>";


