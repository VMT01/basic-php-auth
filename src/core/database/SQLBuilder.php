<?php

namespace app\core\database;

class SQLBuilder
{
    /** @var 'SELECT'|'INSERT'|'UPDATE' $action */
    private string $action = '';
    private ?array $columns = null;
    private string $table = '';
    /** @var array<string, string> $queries */
    private ?array $queries = null;
    // /** @var array<string, string|int> $values */
    // private array $values = [];
    /** @var null|array{order_by?:array{direction:'ASC'|'DESC',key:string},limit?:int,offset?:int} $filter */
    private ?array $filter = null;

    public static function builder(): self
    {
        return new self();
    }

    /** @param array<int, string> $columns Empty if select all */
    public function select(array $columns = []): self
    {
        $this->action = 'SELECT';
        $this->columns = $columns;
        return $this;
    }

    /** @param array<int, string> $columns Empty if select all */
    public function insert(array $columns = []): self
    {
        $this->action = 'INSERT';
        $this->columns = $columns;
        return $this;
    }

    /** @param array<int, string> $columns */
    public function update(array $columns = []): self
    {
        $this->action = 'UPDATE';
        $this->columns = $columns;
        return $this;
    }

    public function table(string $table): self
    {
        $this->table = $table;
        return $this;
    }

    /** @param array<int, string> $conditions */
    public function where(array $queries): self
    {
        $this->queries = $queries;
        return $this;
    }

    /** @param array{order_by?:array{direction:'ASC'|'DESC',key:string},limit?:int,offset?:int} $filter */
    public function filter(array $filter): self
    {
        $this->filter = $filter;
        return $this;
    }

    public function build(): string
    {
        switch ($this->action) {
            case 'SELECT':
                $SQL = sprintf(
                    'SELECT %s FROM %s %s %s;',
                    $this->columns ? implode(', ', $this->columns) : '*',
                    $this->table,
                    $this->queries ? 'WHERE ' . implode('AND ', $this->queries) : null,
                    $this->filter ? sprintf(
                        '%s %s %s',
                        isset($this->filter['limit']) ? 'LIMIT ' . $this->filter['limit'] : null,
                        isset($this->filter['offset']) ? 'OFFSET ' . $this->filter['offset'] : null,
                        isset($this->filter['order_by']) ? 'ORDER BY ' . $this->filter['order_by']['key'] . ' ' . $this->filter['order_by']['direction'] : null
                    ) : null,
                );
                break;
            case 'INSERT':
                $SQL = sprintf(
                    'INSERT INTO %s (%s) VALUES (%s);',
                    $this->table,
                    implode(', ', $this->columns),
                    implode(', ', $this->queries)
                );
                break;
            case 'UPDATE':
                $SQL = sprintf(
                    'UPDATE %s SET %s %s;',
                    $this->table,
                    implode(
                        ', ',
                        array_map(fn(string $column) => "$column = :$column", $this->columns)
                    ),
                    $this->queries ? 'WHERE ' . implode('AND ', $this->queries) : null,
                );
                break;
        }

        return $SQL;
    }
}
