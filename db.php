<?php
date_default_timezone_set("Asia/Taipei");
session_start();
class DB
{

    protected $dsn = "mysql:host=localhost;charset=utf8;dbname=school";
    protected $pdo;
    protected $table;
    // 在實例化物件的當下就會預設，建構執行，所以new DB('$table')一開始就要給table
    // 才會賦予table給這個物件的table，後面的function就是用$this->table來取我們給的table
    // 同時PDO連線資料庫
    public function __construct($table)
    {
        $this->table = $table;
        $this->pdo = new PDO($this->dsn, 'root', '');
    }

    function all($where = '', $other = '')
    {
        $sql = "select * from `$this->table` ";
        $sql = $this->sql_all($sql, $where, $other);
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
    function count($where = '', $other = '')
    {
        $sql = "select count(*) from `$this->table` ";
        $sql = $this->sql_all($sql, $where, $other);
        return $this->pdo->query($sql)->fetchColumn();
    }
    // 聚合函式差別在給予什麼函式及欄位其他都一樣所以自訂math函式
    private function math($math, $col, $array = '', $other = '')
    {
        $sql = "select $math(`$col`) from `$this->table`";
        $sql = $this->sql_all($sql, $array, $other);
        return $this->pdo->query($sql)->fetchColumn();
    }
    function sum($col = '', $where = '', $other = '')
    {
        return  $this->math('sum', $col, $where, $other);
    }
    function max($col, $where = '', $other = '')
    {
        return  $this->math('max', $col, $where, $other);
    }
    function min($col, $where = '', $other = '')
    {
        return  $this->math('min', $col, $where, $other);
    }


    function find($id)
    {

        $sql = "select * from `$this->table` ";

        if (is_array($id)) {
            $tmp = $this->a2s($id);
            $sql .= " where " . join(" && ", $tmp);
        } else if (is_numeric($id)) {
            $sql .= " where `id`='$id'";
        } else {
            echo "錯誤:參數的資料型態比須是數字或陣列";
        }
        //echo 'find=>'.$sql;
        $row = $this->pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
        return $row;
    }
    // 判斷是否有id欄位有id更新資料，指定where條件的id，沒有id就是新增資料
    function save($array)
    {
        if (isset($array['id'])) {
            $sql = "update `$this->table` set ";

            if (!empty($array)) {
                $tmp = $this->a2s($array);
            } else {
                echo "錯誤:缺少要編輯的欄位陣列";
            }
            $sql .= join(",", $tmp);
            $sql .= " where `id`='{$array['id']}'";
        } else {
            $sql = "insert into `$this->table` ";
            $cols = "(`" . join("`,`", array_keys($array)) . "`)";
            $vals = "('" . join("','", $array) . "')";

            $sql = $sql . $cols . " values " . $vals;
        }
        return $this->pdo->exec($sql);
    }


    function del($id)
    {

        $sql = "delete from `$this->table` where ";

        if (is_array($id)) {

            $tmp = $this->a2s($id);
            $sql .= join(" && ", $tmp);
        } else if (is_numeric($id)) {
            $sql .= " `id`='$id'";
        } else {
            echo "錯誤:參數的資料型態比須是數字或陣列";
        }
        //echo $sql;

        return $this->pdo->exec($sql);
    }
    // 用來查找沒有在上述自訂function，查找其他的可以使用
    function query($sql)
    {
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
    //轉換陣列顯示的方式`$col`='$value'存放在新的$tmp陣列，這個值在用各個語法的方式取用
    private function a2s($array)
    {
        foreach ($array as $col => $value) {
            $tmp[] = "`$col`='$value'";
        }
        return $tmp;
    }
    // sql語法組合:如果是陣列會轉換成sql語法，如果不是就是字串回傳，回傳sql完整語法
    // 因為好幾個function都會用到判斷sql語法如何組合串起來所以自訂function
    private function sql_all($sql, $array, $other)
    {
        if (isset($this->table) && !empty($this->table)) {

            if (is_array($array)) {
                if (!empty($array)) {
                    $tmp = $this->a2s($array);
                    $sql .= " where " . join(" && ", $tmp);
                }
            } else {
                $sql .= " $array";
            }

            $sql .= $other;
            //echo 'all=>'.$sql;
            // $rows = $this->pdo->query($sql)->fetchColumn();
            return $sql;
        } else {
            echo "錯誤:沒有指定的資料表名稱";
        }
    }
}
// 排版好閱讀陣列
function dd($array)
{
    echo "<pre>";
    print_r($array);
    echo "</pre>";
}



$student = new DB('student_scores');
$rows = $student->count();
dd($rows);
echo "<hr>";
$Score = new DB('student_scores');
$sum = $Score->sum('score');
dd($sum);
echo "<hr>";
$sum = $Score->sum('score', " where `school_num` <= '911020'");
dd($sum);
echo "<hr>";
$sum = $Score->max('score');
dd($sum);
