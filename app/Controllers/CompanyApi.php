<?php

namespace App\Controllers;

use App\Models\CompanyModel;
use CodeIgniter\API\ResponseTrait;

class CompanyApi extends BaseController
{
    use ResponseTrait;

    public function schema($id)
    {
        if (!session()->get('isLoggedIn')) {
            return $this->failUnauthorized('Not authenticated');
        }
        $model = new CompanyModel();
        $row = $model->find((int)$id);
        if (!$row || $row['status'] !== 'active') {
            return $this->failNotFound('Company not found');
        }
        $schema = $model->getSchemaArray((int)$id);
        return $this->respond([ 'id' => (int)$id, 'name' => $row['name'], 'schema' => $schema ]);
    }
}
