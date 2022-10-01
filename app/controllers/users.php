<?php 

class Users extends Controller 
{
    public function __construct()
    {
        $this->userModel = $this->model('User');
    }


    public function register()
    {
        // Cek jika POST
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Bersihkan data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);


            // init data
            $data = [
                'fullname' => trim($_POST['fullname']),
                'username' => trim($_POST['username']),
                'password' => trim($_POST['password']),
                'confirm_password' => trim($_POST['confirm_password']),
                'fullname_err' => '',
                'username_err' => '',
                'password_err' => '',
                'confirm_password_err' => '',
            ];

            // jika fullname tidak ada
            if (empty($data['fullname'])) {
                $data['fullname_err'] = 'Mohon masukkan nama lengkap anda';
            }
            
            // jika username tidak ada
            if (empty($data['username'])) {
                $data['username_err'] = 'Mohon masukkan nama akun anda';
            } elseif ($this->userModel->cariUsername($data['username'])) {
                $data['username_err'] = 'Nama akun sudah digunakan';
            }

            // jika password tidak ada
            if (empty($data['password'])) {
                $data['password_err'] = 'Mohon masukkan password';
            }

            // jika nama tidak ada
            if (empty($data['confirm_password'])) {
                $data['confirm_password_err'] = 'Mohon konfirmasi password';
            } elseif ($data['password'] != $data['confirm_password']) {
                $data['confirm_password_err'] = 'Password tidak cocok';
            }

            // memastikan tidak ada error
            if (empty($data['name_err']) && empty($data['email_err']) && empty($data['password_err']) && empty($data['confirm_password_err']) ) {
                // user tervalidasi dan data akan dimasukkan ke database
                // Hash password
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

                // Daftarkan user
                if ($this->userModel->register($data)) {
                    flash('register_success', 'Anda berhasil mendaftar, silahkan melakukan login');
                    redirect('users/login');
                } else {
                    die("Gagal Mendaftarkan Akun [Model: User->register was returning false]");
                }

            } else {
                // Tampilkan view dengan data
                $this->view('users/register', $data);
            }


        } else {
            $data = [
                'fullname' => '',   
                'username' => '',
                'password' => '',
                'confirm_password' => '',
                'fullname_err' => '',
                'username_err' => '',
                'password_err' => '',
                'confirm_password_err' => '',
            ];

            $this->view('users/register', $data);
        }
    }

    public function login()
    {
        // Cek jika POST
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Process form
            // Bersihkan data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $data = [
                'username' => trim($_POST['username']),
                'password' => trim($_POST['password']),
                'username_err' => '',
                'password_err' => '',
            ];

             // jika email tidak ada
             if (empty($data['username'])) {
                $data['username_err'] = 'Mohon masukkan nama akun anda';
            } elseif (!$this->userModel->cariUsername($data['username'])) {
                $data['username_err'] = 'Username tidak ditemukan';   
            }

            // jika password tidak ada
            if (empty($data['password'])) {
                $data['password_err'] = 'Mohon masukkan password';
            }

            // memastikan tidak ada error
            if (empty($data['username_err']) && empty($data['password_err'])) {
                // user tervalidasi 
                $loggedInUser = $this->userModel->login($data['username'], $data['password']);
                
                if ($loggedInUser) {
                    // buat session
                    $this->createUserSession($loggedInUser);

                }else {
                    // password tidak cocok dengan database
                    $data['password_err'] = 'Password Salah';

                    // kembali ke view dengan error password
                    $this->view('users/login', $data);
                }
            } else {
                // Tampilkan view dengan data
                $this->view('users/login', $data);
            }

        } else {
            $data = [
                'username' => '',
                'password' => '',
                'username_err' => '',
                'password_err' => '',
            ];

            $this->view('users/login', $data);
        }
    }


    public function createUserSession($user)
    {
        $_SESSION['user_id'] = $user->uid;
        $_SESSION['user_name'] = $user->username;
        redirect('pages/index');
    }

    public function logout()
    {
        unset($_SESSION['user_id']);
        unset($_SESSION['user_name']);

        session_destroy();

        redirect('users/login');
    }

    public function follow($followedid)
    {
        if (isLoggedIn()) {
            $this->userModel->addFollower($_SESSION['user_id'], $followedid);
            redirect('pages/account/' . $followedid);
        } else {
            $this->view('pages/index');
        }
    }

    public function disfollow($followedid)
    {
        if (isLoggedIn()) {
            $this->userModel->disFollow($_SESSION['user_id'], $followedid);
            redirect('pages/account/' . $followedid);
        } else {
            $this->view('pages/index');
        }
    }

    public function comment()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $data = [
                'uid' => $_SESSION['user_id'],
                'comment' => $_POST['comment'],
                'post_id' => $_POST['post_id']
            ];

            $result = $this->userModel->postComment($data["uid"], $data["comment"], $data["post_id"]);

            redirect('pages/comment/' . $data['post_id']);
        }
    }

    public function edit_profile()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $data = [
                "username" => $_POST['username'],
                "nickname" => $_POST['nickname'],
                "fullname" => $_POST['fullname'],
                "email" => $_POST['email'],
                "profile_picture" => "",
                "image_err" => ""
            ];

            $target_file = "";

            if ($_FILES['profile_picture']['name'] && $_FILES['profile_picture']['name'] != "") {

                $imageFileType = strtolower(pathinfo(basename($_FILES["profile_picture"]["name"],PATHINFO_EXTENSION))['extension']);
                $new_file_name = generateUidV4() . '.' . $imageFileType;
                $target_file = UPLOADDIR . '/' . $new_file_name;

                // generate lagi nama file jika sudah pernah digunakan
                while (file_exists($target_file)) {
                    $new_file_name = generateUidV4() . '.' . $imageFileType;
                    $target_file = UPLOADDIR . '/' . $new_file_name;
                }
                $data['profile_picture'] = $new_file_name;
    
                // Check if image file is a actual image or fake image
                if(isset($_POST["submit"])) {
                    $check = getimagesize($_FILES["profile_picture"]["tmp_name"]);
                    if($check == false) {
                        $data['image_err'] = "file bukan gambar/foto";
                    }
                }
                
                // Check file size
                if ($_FILES["profile_picture"]["size"] > 100000000) {
                    $data['image_err'] = "Ukuran gambar tidak boleh lebih dari 100mb";
                }

                if (empty($data['image_err'])) {

                    move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file);
                    $this->userModel->edit_profile(
                        $data['username'],
                        $data['nickname'],
                        $data['fullname'],
                        $data['email'],
                        $data['profile_picture']
                    );
                    redirect('pages/account');                    
                } else {
                    redirect('pages/edit_profile');
                }

            } else {
                if (empty($data['image_err'])) {
                    $this->userModel->edit_profile(
                        $data['username'],
                        $data['nickname'],
                        $data['fullname'],
                        $data['email'],
                        null
                    );
                    redirect('pages/account');                  
                } else {
                    redirect('pages/edit_profile');
                }
            }
        }
    }
}
