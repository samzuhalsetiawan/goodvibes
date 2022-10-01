<?php 

class User 
{
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    // Cari Username
    public function cariUsername(string $username)
    {
        $this->db->query("SELECT * FROM users WHERE username = :username");
        $this->db->bind(':username', $username);

        $row = $this->db->single();

        if ($this->db->rowCount() > 0) {
            return true;
        }else {
            return false;
        }
    }


    public function register($data)
    {
        // register
        $this->db->query("INSERT INTO users(uid, username, hash_pass) VALUES (:uid, :username, :hash_pass)");
        
        // bindvalues
        $uid = generateUidV4();
        $this->db->bind(':uid', $uid);
        $this->db->bind(':username', $data['username']);
        $this->db->bind(':hash_pass', $data['password']);

        // execute
        if ($this->db->execute()) {
            $this->db->query("INSERT INTO detail_users(id_fk_users_uid, fullname) VALUES (:uid, :fullname)");
            $this->db->bind(':uid', $uid);
            $this->db->bind(':fullname', $data['fullname']);
            if ($this->db->execute()) {
                return true;
            } else {
                return false;
            }
        }else {
            return false;
        }
    
    }


    public function login($username, $password)
    {
        $this->db->query("SELECT * FROM users WHERE username = :username");
        $this->db->bind(':username', $username);

        $row = $this->db->single();

        $hashed_password = $row->hash_pass;

        if (password_verify($password, $hashed_password)) {
            return $row;
        }else {
            return false;
        }
    }

    public function getUserAllPosts($userid)
    {
        $data = [
            "username" => "",
            "profile_picture" => "",
            "posts" => []
        ];

        $this->db->query(
            "SELECT 
                users.username AS username,
                detail.profile_picture AS profile_picture
             FROM users
             INNER JOIN detail_users AS detail
             ON users.uid = detail.id_fk_users_uid
             WHERE users.uid = :uid"
            );
        $this->db->bind(":uid", $userid);

        $user = $this->db->single();

        
        $data['username'] = $user->username;
        $data['profile_picture'] = $user->profile_picture;
        
        
        $this->db->query(
            "SELECT 
                posts.id AS post_id,
                posts.owner_fk_detail_users_id AS owner,
                users.username AS username,
                detail.profile_picture AS profile_picture,
                posts.caption AS caption,
                posts.media AS media,
                posts.created_at AS created_at
             FROM posts
             INNER JOIN detail_users AS detail
             ON posts.owner_fk_detail_users_id = detail.id_fk_users_uid
             INNER JOIN users
             ON detail.id_fk_users_uid = users.uid
             LEFT JOIN followers
             ON followers.followed_fk_detail_users_id = posts.owner_fk_detail_users_id
             WHERE
                followers.follower_fk_detail_users_id = :userid
             OR
             posts.owner_fk_detail_users_id = :userid
             ORDER BY posts.created_at"
        );
        $this->db->bind(":userid", $userid);
        
        $data['posts'] = $this->db->resultSet();
        
        return $data;
    }

    public function posting($post)
    {
        $data = [
            "id" => generateUidV4(),
            "owner_fk_detail_users_id" => $post['uid'],
            "caption" => $post['caption'],
            "media" => $post['file_name']
        ];

        $this->db->query(
            "INSERT INTO posts(id, owner_fk_detail_users_id, caption, media)
            VALUES(:id, :owner, :caption, :media)"
        );
        $this->db->bind(":id", $data['id']);
        $this->db->bind(":owner", $data['owner_fk_detail_users_id']);
        $this->db->bind(":caption", $data['caption']);
        $this->db->bind(":media", $data['media']);

        if($this->db->execute()) {
            return $data['id'];
        } else {
            return false;
        }
    }

    public function getAcountData($userid)
    {
        $data = [
            "uid" => "",
            "username" => "",
            "profile_picture" => "",
            "followers" => 0,
            "following" => 0,
            "posts" => []
        ];

        $this->db->query(
            "SELECT
                users.uid as uid,
                users.username AS username,
                detail.profile_picture AS profile_picture
            FROM users
            INNER JOIN detail_users AS detail
            ON users.uid = detail.id_fk_users_uid
            WHERE users.uid = :uid"
        );
        $this->db->bind(":uid", $userid);
        $user = $this->db->single();

        $data['uid'] = $user->uid;
        $data['username'] = $user->username;
        $data['profile_picture'] = $user->profile_picture;

        $this->db->query(
            "SELECT
                id AS post_id,
                caption,
                media
            FROM posts
            WHERE owner_fk_detail_users_id = :uid
            ORDER BY created_at"
        );
        $this->db->bind(":uid", $userid);
        $posts = $this->db->resultSet();

        $data['posts'] = $posts;

        $this->db->query(
            "SELECT
                COUNT(followed_fk_detail_users_id) AS follower_count
            FROM followers
            WHERE followed_fk_detail_users_id = :uid"
        );
        $this->db->bind(":uid", $userid);
        $followers = $this->db->single();

        $data['followers'] = $followers->follower_count;
        
        $this->db->query(
            "SELECT
                COUNT(follower_fk_detail_users_id) AS following_count
            FROM followers
            WHERE follower_fk_detail_users_id = :uid"
        );
        $this->db->bind(":uid", $userid);
        $followers = $this->db->single();

        $data['following'] = $followers->following_count;
        
        return $data;
    }

    public function searchUserByUsername($username)
    {
        $data = [
            'users' => []
        ];

        
        $this->db->query(
            "SELECT
                users.uid AS uid,
                users.username AS username,
                detail.profile_picture AS profile_picture
            FROM users
            INNER JOIN detail_users AS detail
            ON users.uid = detail.id_fk_users_uid
            WHERE users.username LIKE CONCAT( '%', :username, '%')"
        );

        $this->db->bind(":username", $username);

        $result = $this->db->resultSet(PDO::FETCH_ASSOC);
        $data['users'] = $result;

        return $data;
    }

    public function addFollower($followerid, $followedid)
    {
        $id = generateUidV4();

        $this->db->query(
            "INSERT INTO followers
            VALUES(:id, :followerid, :followedid)"
        );

        $this->db->bind(":id", $id);
        $this->db->bind(":followerid", $followerid);
        $this->db->bind(":followedid", $followedid);

        if ($this->db->rowCount() > 0) {
            return true;
        }
        return false;
    }

    public function isFollowing($followerid, $followedid)
    {
        $this->db->query(
            "SELECT *
            FROM followers
            WHERE
                follower_fk_detail_users_id = :followerid
            AND
                followed_fk_detail_users_id = :followedid"
        );
        $this->db->bind(":followerid", $followerid);
        $this->db->bind(":followedid", $followedid);

        $result = $this->db->single();

        if ($result) {
            return true;
        }
        return false;
    }

    public function disFollow($followerid, $followedid)
    {
        $this->db->query(
            "DELETE FROM followers
            WHERE follower_fk_detail_users_id = :followerid
            AND followed_fk_detail_users_id = :followedid"
        );

        $this->db->bind(":followerid", $followerid);
        $this->db->bind(":followedid", $followedid);

        if ($this->db->rowCount() > 0) {
            return true;
        }
        return false;
    }

    public function getPostComments($postid)
    {
        $data = [
            "owner" => [
                "post_id" => $postid,
                "profile_picture" => "",
                "username" => "",
                "caption" => "",
                "media" => ""
            ],
            "comments" => []
        ];

        $this->db->query(
            "SELECT
                posts.id AS post_id,
                users.username AS username,
                detail.profile_picture AS profile_picture,
                posts.caption AS caption,
                posts.media AS media
            FROM users
            INNER JOIN detail_users AS detail
            ON users.uid = detail.id_fk_users_uid
            INNER JOIN posts
            ON detail.id_fk_users_uid = posts.owner_fk_detail_users_id
            WHERE posts.id = :postid"
        );
        $this->db->bind(":postid", $postid);

        $data['owner'] = $this->db->single();

        $this->db->query(
            "SELECT
                users.username AS username,
                detail.profile_picture AS profile_picture,
                comments.comment AS comment
            FROM comments
            INNER JOIN detail_users AS detail
            ON comments.owner_fk_detail_users_id = detail.id_fk_users_uid
            INNER JOIN users
            ON comments.owner_fk_detail_users_id = users.uid
            WHERE comments.fk_posts_id = :postid
            ORDER BY created_at"
        );
        $this->db->bind(":postid", $postid);

        $data['comments'] = $this->db->resultSet(); 
        return $data;
    }

    public function getProfileUser($userid)
    {
        $data = [
            "uid" => $userid,
            "username" => "",
            "profile_picture" => "",
            "nicname" => "",
            "fullname" => "",
            "email" => ""
        ];

        $this->db->query(
            "SELECT 
                users.username AS username,
                detail.profile_picture AS profile_picture,
                detail.nickname AS nickname,
                detail.fullname AS fullname,
                detail.email AS email
            FROM users
            INNER JOIN detail_users AS detail
            ON users.uid = detail.id_fk_users_uid
            WHERE users.uid = :userid"
        );

        $this->db->bind(":userid", $userid);

        $user = $this->db->single();

        $data["username"] = $user->username;
        $data["profile_picture"] = $user->profile_picture;
        $data["nickname"] = $user->nickname;
        $data["fullname"] = $user->fullname;
        $data["email"] = $user->email;

        return $data;
    }

    public function postComment($uid, $comment, $post_id)
    {
        $data = [
            "id" => generateUidV4(),
            "post_id" => $post_id,
            "owner" => $uid,
            "comment" => $comment
        ];

        $this->db->query(
            "INSERT INTO comments (id, fk_posts_id, owner_fk_detail_users_id, comment) 
            VALUES (:id, :post_id, :owner, :comment)"
        );
        $this->db->bind(":id", $data["id"]);
        $this->db->bind(":post_id", $data["post_id"]);
        $this->db->bind(":owner", $data["owner"]);
        $this->db->bind(":comment", $data["comment"]);

        if($this->db->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function edit_profile(
        string $username,
        string $nickname,
        string $fullname,
        string $email,
        string $profile_picture = null
    )
    {

        $this->db->query(
            "UPDATE users
            SET username = :username
            WHERE uid = :uid"
        );
        $this->db->bind(":username", $username);
        $this->db->bind(":uid", $_SESSION['user_id']);

        $resultUsers = $this->db->rowCount();

        $this->db->query(
            "UPDATE detail_users
            SET
                nickname = :nickname,
                fullname = :fullname,
                email = :email
            WHERE id_fk_users_uid = :uid"
        );
        $this->db->bind(":nickname", $nickname);
        $this->db->bind(":fullname", $fullname);
        $this->db->bind(":email", $email);
        $this->db->bind(":uid", $_SESSION['user_id']);

        $resultDetaiUsers = $this->db->rowCount();
        
        if ($profile_picture) {
            $this->db->query(
                "UPDATE detail_users
                SET
                    profile_picture = :profile_picture
                WHERE id_fk_users_uid = :uid"
            );
            $this->db->bind(":profile_picture", $profile_picture);
            $this->db->bind(":uid", $_SESSION['user_id']);
    
            $resultUpdatePP = $this->db->rowCount();
        }
    }
}
