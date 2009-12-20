<?php
require_once( 'base.php' );
class base_model extends base
{
	private $orderBy = '';
	private $where = '';
	private $field = '*';
	private $limit = '';
	private $join = '';
	private $page;
	private $pageSize;
    protected $pdo;

    function __construct()
    {
    	if( empty( $this->pdo ) )
    	{
    		$this->set_pdo(SYS_DB_HOST, SYS_DB_NAME,
    		               SYS_DB_USER, SYS_DB_PASSWD);
    	}
    }

    protected function set_pdo( $host, $db_name, $user, $passwd )
    {
        $dsn = 'mysql:host=' . $host .';dbname=' . $db_name;
        $this->pdo = new PDO($dsn, $user, $passwd);
    }

    protected function save( $table, $data, $pk = 'id' )
    {
        if( $data[$pk] ) // update
        {
            $ret = $this->update( $table, $data, $pk = 'id' );
        }
        else // insert
        {
            $ret = $this->insert( $table, $data );
        }

        return $ret;
    }

    protected function update( $table, $data, $pk = 'id' )
    {
        $ret = array( STATUS_CODE => 0 );

        // 检查更新数据是否为空
        if ( empty( $data ) )
        {
            $ecode = 1003;
            $ret[STATUS_CODE] = $ecode;
            $ret[MSG] = $GLOBALS[SYS_NAME][SYS_MSG][$ecode];
            return $ret;
        }

        $where = "`{$pk}`='" . mysql_escape_string( $data[$pk] ) . "'";
        unset( $data[$pk] );

        $str_key_value = $this->c2w( $data, ', ' );
        $update_sql  = "UPDATE `{$table}` SET {$str_key_value} WHERE {$where}";
        $update_sql .= " LIMIT 1";

        // 执行更新操作
        $count = $this->pdo->exec( $update_sql );
        $err_code = $this->pdo->errorCode();
        if( '00000' != $err_code and null != $err_code )// 更新出错
        {
            $errorInfo = $this->pdo->errorInfo();
            $record_err  = $errorInfo[2] . 'sql: ' . $update_sql;
            $record_err .= 'error code: ' . $err_code;
            sys_log::report( $record_err );
            $ecode = 1004;
            $ret[STATUS_CODE] = $ecode;
            $ret[MSG] = $errorInfo[2];

            return $ret;
        }

        // 更新成功，返回更新行数
        $ret['count'] = $count;
        return $ret;
    }

    protected function insert( $table, $data )
    {
        $ret = array( STATUS_CODE => 0 );

        // 检查插入数据是否为空
        if ( empty( $data ) )
        {
            $ecode = 1002;
            $ret[STATUS_CODE] = $ecode;
            $ret[MSG] = $GLOBALS[SYS_NAME][SYS_MSG][$ecode];
            return $ret;
        }

        $str_key_value = $this->c2w( $data, ', ' );
        $insert_sql = "INSERT INTO `{$table}` SET {$str_key_value}";

        // 执行插入操作
        $count = $this->pdo->exec( $insert_sql );
        $err_code = $this->pdo->errorCode();
        if( '00000' != $err_code and null != $err_code )// 插入出错
        {
            $errorInfo = $this->pdo->errorInfo();
            $record_err  = $errorInfo[2] . 'sql: ' . $insert_sql;
            $record_err .= 'error code: ' . $err_code;
            sys_log::report( $record_err );
            $ecode = 1001;
            $ret[STATUS_CODE] = $ecode;
            $ret[MSG] = $errorInfo[2];
            return $ret;
        }

        // 插入成功，返回插入行数
        $ret['count'] = $count;
        return $ret;
    }

    // 将键值对转成sql语句
    private function c2w( $condition, $type = 'and' )
    {
        $arr_key_value = array();
        foreach( $condition AS $key => $value )
        {
            $key_value = '`' . $key . "`='" . mysql_escape_string($value) . "'";
            $arr_key_value[] = $key_value;
        }
        $str_key_value = join(" {$type} ", $arr_key_value);

        return $str_key_value;
    }

    /**
     * 取得记录数量（不推荐使用，推荐使用 get_num）
     * @return int   传回记录条数
     */
    function data_num($table, $condition = array(), $type = 'and' )
    {
    	$ret = array( STATUS_CODE => STATUS_OK );
    	$where = '';
    	if( $condition )
    	{
            $where = ' WHERE ' . $this->c2w( $condition, $type );
    	}

		$sql = 'select count(*) as num from ' . $table . $where;
		$result = $this->pdo->query($sql);
		if( false === $result )
		{
			$ecode = 1005;
			$ret[STATUS_CODE] = $ecode;
            $errorInfo = $this->pdo->errorInfo();
			$ret[MSG]  = $GLOBALS[SYS_NAME][SYS_MSG][$ecode];
			$ret[MSG] .= ' 错误消息： ' . $errorInfo[2];
            $ret[MSG] .= ' SQL: ' . $sql;
			return $ret;
		}

		$row = $result->fetch(PDO::FETCH_ASSOC);
		$ret['num'] = $row['num'];
        return $ret;
    }

    /**
     * 取得记录数量
     * @return int   传回记录条数
     */
    function getNum($table)
    {
        $ret = array( STATUS_CODE => STATUS_OK );
        $where = '';
        if( $this->getWhere() )
        {
            $where = ' WHERE ' . $this->getWhere();
        }

        $sql = 'select count(*) as num from ' . $table . $where;
        $result = $this->pdo->query($sql);
        if( false === $result )
        {
            $ecode = 1008;
            $ret[STATUS_CODE] = $ecode;
            $errorInfo = $this->pdo->errorInfo();
            $ret[MSG]  = $GLOBALS[SYS_NAME][SYS_MSG][$ecode];
            $ret[MSG] .= ' 错误消息： ' . $errorInfo[2];
            $ret[MSG] .= ' SQL: ' . $sql;
            return $ret;
        }

        $row = $result->fetch(PDO::FETCH_ASSOC);
        $ret['num'] = $row['num'];
        return $ret;
    }

    function getOne($table)
    {
        $ret = array( STATUS_CODE => STATUS_OK );

        $field = $this->getField();

        $where = '';
        if( $this->getWhere() )
        {
            $where = ' WHERE ' . $this->getWhere();
        }

        $order_by = '';
        if( $this->getOrderBy() )
        {
            $order_by = 'ORDER BY ' . $this->getOrderBy();
        }

        $sql  = 'SELECT ' . $field . ' FROM ' . "`{$table}`";
        $sql .= $where;
        $sql .= ' ';
        $sql .= $order_by;
        $sql .= ' LIMIT 1';

        $result = $this->pdo->query($sql);
        if( false === $result )
        {
            $ecode = 1006;
            $ret[STATUS_CODE] = $ecode;
            $errorInfo = $this->pdo->errorInfo();
            $ret[MSG]  = $GLOBALS[SYS_NAME][SYS_MSG][$ecode];
            $ret[MSG] .= ' 错误消息： ' . $errorInfo[2];
            $ret[MSG] .= ' SQL: ' . $sql;
            return $ret;
        }
        $row = $result->fetch(PDO::FETCH_ASSOC);
        $ret['data'] = $row;
        return $ret;
    }

    function setOrderBy($field, $type = 'DESC')
    {
        $this->orderBy = '`' . $field . '` ' . $type;
    }

    function getOrderBy()
    {
    	if ( empty( $this->orderBy ) )
    	{
    		return '';
    	}
        return $this->orderBy;
    }

    function setWhere($key, $val, $is_num = 0, $glue = '=', $type = 'and')
    {
        if (empty($key) or empty($val))
        {
        	return false;
        }
        $val = mysql_escape_string($val);

        switch ($glue)
        {
        	case '=':
        		if( !$is_num )
        		{
        			$val = "'{$val}'";
        		}
        		$where = "`{$key}`={$val}";
        		break;
        	case 'LIKE':
                $where = "`{$key}` LIKE %{$val}%";
        		break;
        	case 'IN':
                $where = "`{$key}` IN ({$val})";
        		break;
            default:
            	return false;
        }

        if($this->where)
        {
            $this->where .= " {$type} {$where}";
            return;
        }
        $this->where = $where;
    }

    function getWhere()
    {
        return $this->where;
    }

    function setField($arr)
    {
        $this->field = join( ', ', $arr );
    }

    function getField()
    {
        return $this->field;
    }

    function setPage($page)
    {
        $this->page = $page;
    }

    function getPage()
    {
        return $this->page;
    }

    function setPageSize($pageSize)
    {
        $this->pageSize = $pageSize;
    }

    function getPageSize()
    {
    	return $this->pageSize;
    }

    function setLimit($page, $size)
    {
    	if($page <= 0)
    	{
    		$page = 1;
    	}
    	if($size <= 0)
    	{
    		$size = 10;
    	}

    	$offset = ($page - 1) * $size;

        $this->limit = "{$offset}, {$size}";
    }

    function getLimit()
    {
        return $this->limit;
    }

    function setJoin($table, $condition, $glue='LEFT')
    {
        switch (strtoupper($glue))
        {
        	case 'LEFT':
        	case 'RIGHT':
        		if ($this->join)
        		{
                    $this->join .= " {$glue} JOIN `{$table}` ON {$condition}";
        		}
        		else
        		{
                    $this->join = "{$glue} JOIN `{$table}` ON {$condition}";
        		}
        		break;
        }
    }

    function getJoin()
    {
    	return $this->join;
    }

    function getList($table)
    {
        $ret = array( STATUS_CODE => STATUS_OK );

        $field = $this->getField();

        $join = '';
        if($this->getJoin())
        {
        	$join = $this->getJoin();
        }

        $where = '';
        if( $this->getWhere() )
        {
            $where = 'WHERE ' . $this->getWhere();
        }

        $order_by = '';
        if( $this->getOrderBy() )
        {
            $order_by = 'ORDER BY ' . $this->getOrderBy();
        }

        $limit = '';
        if($this->getPage() or $this->getPageSize())
        {
        	$this->setLimit($this->getPage(), $this->getPageSize());
            $limit = 'LIMIT ' . $this->getLimit();
        }

        $sql  = 'SELECT ' . $field . ' FROM ' . "`{$table}` ";
        $sql .= $join;
        $sql .= ' ';
        $sql .= $where;
        $sql .= ' ';
        $sql .= $order_by;
        $sql .= ' ';
        $sql .= $limit;

        $result = $this->pdo->query($sql);
        if( false === $result )
        {
            $ecode = 1007;
            $ret[STATUS_CODE] = $ecode;
            $errorInfo = $this->pdo->errorInfo();
            $ret[MSG]  = $GLOBALS[SYS_NAME][SYS_MSG][$ecode];
            $ret[MSG] .= ' 错误消息： ' . $errorInfo[2];
            $ret[MSG] .= ' SQL: ' . $sql;
            return $ret;
        }
        $row = $result->fetchAll(PDO::FETCH_ASSOC);
        $ret['data'] = $row;
        return $ret;
    }

    function delData($table)
    {
    	$ret = array(STATUS_CODE => STATUS_OK);
        $where = '';
        if( $this->getWhere() )
        {
            $where = 'WHERE ' . $this->getWhere();
        }

        $limit = '';
        if($this->getPage() or $this->getPageSize())
        {
            $this->setLimit($this->getPage(), $this->getPageSize());
            $limit = 'LIMIT ' . $this->getLimit();
        }

        $sql  = "DELETE FROM {$table} ";
        $sql .= $where;
        $sql .= ' ';
        $sql .= $limit;
        $result = $this->pdo->exec($sql);
        if( false === $result )
        {
            $ecode = 1009;
            $ret[STATUS_CODE] = $ecode;
            $errorInfo = $this->pdo->errorInfo();
            $ret[MSG]  = $GLOBALS[SYS_NAME][SYS_MSG][$ecode];
            $ret[MSG] .= ' 错误消息： ' . $errorInfo[2];
            $ret[MSG] .= ' SQL: ' . $sql;
            return $ret;
        }
        $ret['count'] = $result;
        return $ret;
    }
}