<?php

class Kategori_model
{
    private $table = 'categories';
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    private function buildWhereClause($searchTerm, &$bindings)
    {
        $conditions = [];
        if (!empty($searchTerm)) {
            // Pencarian akan dilakukan pada kolom 'name' atau 'slug'
            $conditions[] = "(name LIKE :searchTerm OR slug LIKE :searchTerm)";
            $bindings['searchTerm'] = ['value' => "%$searchTerm%", 'type' => PDO::PARAM_STR];
        }

        if (empty($conditions)) {
            return "";
        }
        return " WHERE " . implode(' AND ', $conditions);
    }

    public function countAllKategori($searchTerm = null)
    {
        $bindings = [];
        $whereClause = $this->buildWhereClause($searchTerm, $bindings);
        $query = "SELECT COUNT(*) as total FROM " . $this->table . $whereClause;

        $this->db->query($query);
        foreach ($bindings as $key => $param) {
            $this->db->bind($key, $param['value'], $param['type']);
        }

        $result = $this->db->single();
        return $result['total'];
    }

    public function getAllKategori($searchTerm = null, $limit = 10, $offset = 0)
    {
        $bindings = [];
        $whereClause = $this->buildWhereClause($searchTerm, $bindings);

        $limit = (int) $limit;
        $offset = (int) $offset;

        $query = "SELECT * FROM " . $this->table . $whereClause . " ORDER BY id DESC LIMIT $limit OFFSET $offset";

        $this->db->query($query);
        foreach ($bindings as $key => $param) {
            $this->db->bind($key, $param['value'], $param['type']);
        }

        return $this->db->resultSet();
    }

    // Nanti kita tambahkan fungsi tambah, edit, hapus, detail di sini
    public function tambahDataKategori($data)
    {
        $query = "INSERT INTO categories (name, slug, spec_template, image) VALUES (:name, :slug, :spec_template, :image)";

        $this->db->query($query);
        $this->db->bind('name', $data['name']);
        $this->db->bind('slug', $data['slug']);
        $this->db->bind('spec_template', $data['spec_template']);
        $this->db->bind('image', $data['image']);

        $this->db->execute();
        return $this->db->rowCount();
    }
    public function getKategoriById($id)
    {
        $this->db->query('SELECT * FROM ' . $this->table . ' WHERE id=:id');
        $this->db->bind('id', $id);
        return $this->db->single();
    }

    public function updateDataKategori($data)
    {
        $query = "UPDATE categories SET name = :name, slug = :slug, spec_template = :spec_template,image = :image WHERE id = :id";

        $this->db->query($query);
        $this->db->bind('id', $data['id']);
        $this->db->bind('name', $data['name']);
        $this->db->bind('slug', $data['slug']);
        $this->db->bind('spec_template', $data['spec_template']);
        $this->db->bind('image', $data['image']); // Bind data gambar

        $this->db->execute();
        return $this->db->rowCount();
    }
    public function hapusDataKategori($id)
    {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";

        $this->db->query($query);
        $this->db->bind('id', $id, PDO::PARAM_INT);
        $this->db->execute();

        return $this->db->rowCount();
    }
    public function getCategoryBySlug($slug)
    {
        $this->db->query('SELECT * FROM ' . $this->table . ' WHERE slug = :slug');
        $this->db->bind('slug', $slug);
        return $this->db->single();
    }
}
