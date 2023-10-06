<?php
class queryBuilder {
    private $select = [];
    private $from = '';
    private $where = [];
    private $orderBy = '';
    private $limit = '';
    private $groupedWhere = [];
    private $insertData = [];
    private $updateData = [];
    private $deleteTable = '';

    private $params = [];

    public function select($columns = null) {
        if ($columns !== null) {
            $this->select = is_array($columns) ? $columns : [$columns];
        }
        return $this;
    }

    public function from($table = null) {
        if ($table !== null) {
            $this->from = $table;
        }
        return $this;
    }

    public function where($column = null, $operator = null, $value = null) {
        if ($column !== null && $operator !== null && $value !== null) {
            $placeholder = ":param_" . count($this->params);
            $this->where[] = "$column $operator $placeholder";
            $this->params[$placeholder] = $value;
        }
        return $this;
    }

    public function and() {
        $this->where[] = 'AND';
        return $this;
    }

    public function or() {
        $this->where[] = 'OR';
        return $this;
    }

    public function orWhere($column = null, $operator = null, $value = null) {
        if ($column !== null && $operator !== null) {
            $placeholder = ":param_" . count($this->params);
            if ($value === null) {
                $this->where[] = "OR $column $operator NULL";
            } else {
                $this->where[] = "OR $column $operator $placeholder";
                $this->params[$placeholder] = $value;
            }
        }
        return $this;
    }

    public function groupOpen() {
        $this->where[] = '(';
        return $this;
    }

    public function groupClose() {
        $this->where[] = ')';
        return $this;
    }

    public function orderBy($column, $direction = 'ASC') {
        $this->orderBy = "$column $direction";
        return $this;
    }

    public function limit($limit) {
        $this->limit = $limit;
        return $this;
    }

    public function insert($table, $data) {
        $this->insertData = ['table' => $table, 'data' => $data];
        return $this;
    }

    public function update($table, $data) {
        $this->updateData = ['table' => $table, 'data' => $data];
        return $this;
    }

    public function delete($table = null) {
        if ($table !== null) {
            $this->deleteTable = $table;
        }
        return $this;
    }
    
    public function build() {
        $query = "";
        $params = $this->params;
    
        if (!empty($this->select) && !empty($this->from)) {
            $query = "SELECT " . implode(', ', $this->select) .
                    " FROM $this->from";
    
            if (!empty($this->where)) {
                $query .= " WHERE " . implode(' ', $this->where);
            }
    
            if (!empty($this->groupedWhere)) {
                $query .= " " . implode(' ', $this->groupedWhere);
            }
    
            if (!empty($this->orderBy)) {
                $query .= " ORDER BY $this->orderBy";
            }
    
            if (!empty($this->limit)) {
                $query .= " LIMIT $this->limit";
            }
        } elseif (!empty($this->insertData)) {
            $table = $this->insertData['table'];
            $data = $this->insertData['data'];
    
            $columns = implode(', ', array_keys($data));
            $values = array_map(function ($value) use (&$params) {
                $placeholder = ":param_" . count($params);
                $params[$placeholder] = $value;
                return $placeholder;
            }, array_values($data));
    
            $values = implode(', ', $values);
    
            $query = "INSERT INTO $table ($columns) VALUES ($values)";
        } elseif (!empty($this->updateData)) {
            $table = $this->updateData['table'];
            $data = $this->updateData['data'];
    
            $set = [];
            foreach ($data as $key => $value) {
                $placeholder = ":param_" . count($params);
                $params[$placeholder] = $value;
                $set[] = "$key = $placeholder";
            }
    
            if (!empty($this->where)) {
                $where = " WHERE " . implode(' ', $this->where);
            } else {
                $where = '';
            }
    
            $query = "UPDATE $table SET " . implode(', ', $set) . $where;
        } elseif (!empty($this->deleteTable)) {
            $table = $this->deleteTable;
    
            if (!empty($this->where)) {
                $where = " WHERE " . implode(' ', $this->where);
            } else {
                $where = '';
            }
    
            $query = "DELETE FROM $table" . $where;
        }
    
        return [
            'query' => $query,
            'params' => $params
        ];
    }
    

}