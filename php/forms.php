<?

require_once("./db.php");

class Form {
    public $id;
    public $title;
    public $userId;
    public function __construct($id, $title, $userId) {
        $this->id = $id;
        $this->title = $title;
        $this->userId = $userId;
    }
}

function getForms($userId) {
    $query = single_query('
        select id, title, user_id
        from form f
        where user_id = :user_id',
        ['user_id' => $userId]
    );

    $forms = [];
    foreach($query->fetchAll() as $row) {
        array_push($forms, new Form($row['id'], $row['title'], $row['userId']));
    }

    return $forms;
}

insert into user (email, password, username) values
("v.iliev@gmail.com", "Test1234", "vlado02");

insert into form (title, user_id) VALUES
('form 1', 1),
('form 2', 1),
('form 3', 1);