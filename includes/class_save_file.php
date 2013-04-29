<?

class save_file {

    public $last_error = '';
    public $last_error_description = '';
    public $saved_filename = '';
    public $status = false;

   public function save_file($filename, $save_path, $save_name = '') {
        $phrases = app::$phrases;
        
        $this->last_error_description = '';
        $this->saved_filename = '';
        $this->status = false;

        if (!file_exists($save_path)) {
            $this->last_error_description = $phrases['err_wrong_uploader_folder'];
            $this->status = false;
        } else {

           $save_name = str_replace(" ", "_",strtolower(convert2en(iif($save_name, $save_name, basename($filename)))));
           $imtype = strtolower(file_extension($save_name));
         

            while (file_exists(CWD . "/" . $save_path . "/" . $save_name)) {
                $save_name = str_replace(".$imtype", "", $save_name) . "_" . rand(0, 999) . ".$imtype";
            }


            //------------ External File ---------//     
            if (strchr($filename, 'http://') || strchr($filename, 'https://') || strchr($filename, 'ftp://')) {

                // --------------- using curl --------//      
                if (function_exists('curl_init')) {

                    if ($this->curl_check_url($filename)) {
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $filename);
                        curl_setopt($ch, CURLOPT_HEADER, 0);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_USERAGENT, 'ALLOMANI PHP AGENT v' . phpversion());

                        $content = curl_exec($ch);

                        curl_close($ch);
                    } else {
                        $this->last_error_description = str_replace("{url}", $filename, $phrases['err_url_x_invalid']);
                        $this->status = false;
                    }
                } else {
                    //----------- using fopen ----------//
                    if (ini_get('allow_url_fopen') == 0) {
                        $this->last_error_description = $phrases['cp_url_fopen_disabled_msg'];
                        $this->status = false;
                    } else {
                        /* if ($filesize = fetch_remote_filesize($filename))
                          {
                         */
                        @ini_set('user_agent', 'PHP');
                        if (!($handle = @fopen($filename, 'rb'))) {
                            $this->last_error_description = str_replace("{url}", $filename, $phrases['err_url_x_invalid']);
                            $this->status = false;
                        } else {
                            while (!feof($handle)) {
                                $content .= fread($handle, 8192);
                            }
                            @fclose($handle);
                        }
                        /*
                          }else{
                          $this->last_error_description =  str_replace("{url}",$filename,$phrases['err_url_x_invalid']);
                          $this->status=false;
                          } */
                    }
                }

                if ($content) {
                    $fp = @fopen(CWD . "/" . $save_path . "/" . $save_name, 'wb');
                    if ($fp) {
                        @fwrite($fp, $content);
                        @fclose($fp);
                        $this->saved_filename = $save_path . "/" . $save_name;
                        $this->status = true;
                    } else {
                        $this->last_error_description = $phrases['err_wrong_uploader_folder'];
                        $this->status = false;
                    }
                } else {
                    $this->last_error_description = iif($this->last_error_description, $this->last_error_description, 'Unknown Error');
                    $this->status = false;
                }
            } else {
                //----------- internal uploaded file ------------//   

                @move_uploaded_file($filename, CWD . "/" . $save_path . "/" . $save_name);

                if (file_exists(CWD . "/" . $save_path . "/" . $save_name)) {
                    $this->saved_filename = $save_path . "/" . $save_name;
                    $this->status = true;
                } else {
                    $this->last_error_description = iif($this->last_error_description, $this->last_error_description, 'Unknown Error');
                    $this->status = false;
                }
            }
        }
    }

   public function curl_check_url($url) {
        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, $url);
        curl_setopt($c, CURLOPT_HEADER, 1); // get the header
        curl_setopt($c, CURLOPT_NOBODY, 1); // and *only* get the header
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1); // get the response as a string from curl_exec(), rather than echoing it
        curl_setopt($c, CURLOPT_FRESH_CONNECT, 1); // don't use a cached version of the url
        if (!curl_exec($c)) {
            return false;
        }
        $httpcode = curl_getinfo($c, CURLINFO_HTTP_CODE);
        return ($httpcode < 400);
    }

}