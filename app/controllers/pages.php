<?php 

class Pages extends Controller
{
    public $currentUserId = "";
    
    public function __construct()
    {
        $this->userModel = $this->model('User');
    }

    public function index($dataTambahan = [])
    {
        if(isLoggedIn())
        {
            $this->currentUserId = $_SESSION['user_id'];
            $data = $this->userModel->getUserAllPosts($this->currentUserId);
            $this->view('pages/index', array_merge($data, $dataTambahan));
        }
        else
        {
            redirect('users/login');
        }
    }

    public function posting()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $data = [
                "uid" => $_POST['uid'],
                "caption" => $_POST['caption'] ?? null,
                "file_name" => $_FILES['media']['name'] ?? null,
                "caption_err" => "",
                "image_err" => ""
            ];

            $target_file = '';

            // Cek jika tidak ada postingan
            if (is_null($data['caption']) && is_null($data['file_name'])) {
                $data['caption_err'] = "Minimal harus ada 1 caption atau foto";
            }
            
            if ($_FILES['media']['name'] && $_FILES['media']['name'] != "") {

                $imageFileType = strtolower(pathinfo(basename($_FILES["media"]["name"],PATHINFO_EXTENSION))['extension']);
                $new_file_name = generateUidV4() . '.' . $imageFileType;
                $target_file = UPLOADDIR . '/' . $new_file_name;
                // generate lagi nama file jika sudah pernah digunakan
                while (file_exists($target_file)) {
                    $new_file_name = generateUidV4() . '.' . $imageFileType;
                    $target_file = UPLOADDIR . '/' . $new_file_name;
                }
                $data['file_name'] = $new_file_name;
    
                // Check if image file is a actual image or fake image
                if(isset($_POST["submit"])) {
                    $check = getimagesize($_FILES["media"]["tmp_name"]);
                    if($check == false) {
                        $data['image_err'] = "file bukan gambar/foto";
                    }
                }
                
                // Check file size
                if ($_FILES["media"]["size"] > 100000000) {
                    $data['image_err'] = "Ukuran gambar tidak boleh lebih dari 100mb";
                }

            }
            
            if (empty($data['caption_err']) && empty($data['image_err'])) {

                if ($_FILES['media']['name']) {
                    move_uploaded_file($_FILES["media"]["tmp_name"], $target_file);
                }
                if ($this->userModel->posting($data)) {
                    flash('posting_success', 'Selamat, Postingan anda berhasil di post');
                    $this->index();
                } else {
                    echo "Gagal Memposting [Error]";
                }
                
            } else {
                $this->index($data);
            }
            
        } else {
            $this->index();
        }
    }

    public function account($userid = null)
    {
        if(isLoggedIn())
        {
            if ($userid == null) {
                $userid = $_SESSION['user_id'];
                $data = $this->userModel->getAcountData($userid);
                $this->view('pages/account', $data);
            } else {
                $data = $this->userModel->getAcountData($userid);
                $followStatus = $this->userModel->isFollowing($_SESSION['user_id'], $userid);
                $data['is_following'] = $followStatus;
                $this->view('pages/account', $data);
            }
        }
        else
        {
            redirect('users/login');
        }
    }

    public function searchUser()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $body = json_decode(file_get_contents('php://input'), true);

            if (isset($body['username'])) {
                $data = $this->userModel->searchUserByUsername($body['username']);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode($data);
            }
        }
    }

    public function comment($postid)
    {
        if(isLoggedIn())
        {
            $user = $this->userModel->getProfileUser($_SESSION['user_id']);
            $comments = $this->userModel->getPostComments($postid);
            $this->view('pages/comment', array_merge(["user" => $user], $comments));

        }
        else
        {
            redirect('users/login');
        }
    }

    public function edit_profile()
    {
        if(isLoggedIn())
        {
            $data = $this->userModel->getProfileUser($_SESSION['user_id']);
            $this->view('pages/edit_profile', $data);
        }
        else
        {
            redirect('users/login');
        }
    }
}
