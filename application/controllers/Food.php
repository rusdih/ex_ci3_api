<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Food extends CI_Controller {

	public function index()
	{
		$r_foods = $this->db->get('foods')->result();

        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode($r_foods);
	}

    public function create(){
        // Pastikan method adalah POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        // Konfigurasi upload
        $config['upload_path']   = './uploads/';
        $config['allowed_types'] = 'jpg|jpeg|png|gif';
        $config['max_size']      = 204800; // 2MB
        $config['encrypt_name']  = TRUE;

        $this->load->library('upload', $config);

        //upload file, jika gagal masuk ke kondisi
         if (!$this->upload->do_upload('image')) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo "blehhhhh";
            // echo json_encode([
            //     'error' => $this->upload->display_errors(),
            //     'message' => "gagal upload image"
            // ]);
            return;
        }

        $upload_data = $this->upload->data();

         // Ambil data post
        $title = $this->input->post('title');
        $description = $this->input->post('description');


        // Simpan ke database
        $data = [
            'title'       => $title,
            'description' => $description,
            'image'       => $upload_data['file_name'] // hanya nama file
        ];
        $insert = $this->db->insert('foods', $data);

         if ($insert) {
            http_response_code(201);
            echo json_encode(['message' => 'Food created successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to insert data']);
        }
    }

    public function update($id = null){
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($id)) {
            http_response_code(405);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        //cek apabila data ada
        $this->db->where('id', $id);
        $q_foods = $this->db->get('foods');
        if(!$q_foods->num_rows()){
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Not Found']);
            return;
        }

        if(!empty($this->input->post('image'))){
            // Konfigurasi upload
            $config['upload_path']   = './uploads/';
            $config['allowed_types'] = 'jpg|jpeg|png|gif';
            $config['max_size']      = 204800; // 2MB
            $config['encrypt_name']  = TRUE;

            $this->load->library('upload', $config);

            //upload file, jika gagal masuk ke kondisi
            if (!$this->upload->do_upload('image')) {
                http_response_code(400);
                echo json_encode(['error' => $this->upload->display_errors()]);
                return;
            }

            $upload_data = $this->upload->data();
        }else{
            $r_foods = $q_foods->result();
            $upload_data['file_name'] = $r_foods[0]->image;
        }
        

         // Ambil data post
        $title = $this->input->post('title');
        $description = $this->input->post('description');


        // Simpan ke database
        $data_update = [
            'title'       => $title,
            'description' => $description,
            'image'       => $upload_data['file_name'] // hanya nama file
        ];
        $this->db->where('id', $id);
        $updated = $this->db->update('foods', $data_update);

         if ($updated) {
            http_response_code(200);
            echo json_encode(['message' => 'Food updated successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update data']);
        }
    }

    public function delete($id = null){
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($id)) {
            http_response_code(405);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

         //cek apabila data ada
        $this->db->where('id', $id);
        $q_foods = $this->db->get('foods');
        if(!$q_foods->num_rows()){
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Not Found']);
            return;
        }

        $this->db->where('id', $id);
        $deleted = $this->db->delete('foods');

        if ($deleted) {
            http_response_code(200);
            echo json_encode(['message' => 'Food deleted successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to delete food']);
        }
    }
}
