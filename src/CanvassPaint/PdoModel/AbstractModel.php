<?php

namespace CanvassPaint\PdoModel;

class AbstractModel implements \ArrayAccess
{
    /** @var \PDO */
    protected $db;
    /** @var string */
    protected static $table = '';
    /** @var array */
    protected $data;
    /** @var array */
    protected $columns = [];

    public function __construct(\PDO $db, array $data = [])
    {
        $this->db = $db;

        $this->data = $data;
    }

    public function find($id, $owner_id = null)
    {
        $sql = 'SELECT * FROM ' . static::getTable() . ' WHERE id = :id';

        $params = [':id' => $id];

        if (null !== $owner_id) {
            $sql .= ' AND owner_id = :owner_id';

            $params[':owner_id'] = $owner_id;
        }

        $this->data = $this->fetch($sql, $params);

        return $this;
    }

    /**
     * @param string $sql
     * @param array|null $params
     * @return \CanvassPaint\PdoModel\AbstractModel
     */
    protected function fetchModel(string $sql, array $params = null)
    {
        $row = $this->fetch($sql, $params);

        return new static($this->db, $row);
    }

    /**
     * @param string $sql
     * @param array|null $params
     * @return \CanvassPaint\PdoModel\AbstractModel[]
     */
    protected function fetchModels(string $sql, array $params = null)
    {
        $rows = $this->fetchAll($sql, $params);

        $models = [];

        foreach ($rows as $row) {
            $models[] = new static($this->db, $row);
        }

        return $models;
    }

    /**
     * @param string $sql
     * @param array|null $params
     * @param int $fetch_style
     * @return array
     */
    protected function fetch(
        string $sql,
        array $params = null,
        int $fetch_style = \PDO::FETCH_ASSOC
    )
    {
        $statement = $this->db->prepare($sql);

        $statement->execute($params);

        return $statement->fetch($fetch_style);
    }

    /**
     * @param string $sql
     * @param array|null $params
     * @param int $fetch_style
     * @return array
     */
    protected function fetchAll(
        string $sql,
        array $params = null,
        int $fetch_style = \PDO::FETCH_ASSOC
    )
    {
        $statement = $this->db->prepare($sql);

        $statement->execute($params);

        return $statement->fetchAll($fetch_style);
    }

    public function getId()
    {
        return $this->data['id'] ?? null;
    }

    public function getData($key)
    {
        return $this->data[$key] ?? null;
    }

    public function setData($key, $value)
    {
        $this->data[$key] = $value;
    }

    /** @return string */
    public static function getTable()
    {
        return static::$table;
    }

    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->data[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }
}
