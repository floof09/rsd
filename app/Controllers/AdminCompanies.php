<?php

namespace App\Controllers;

use App\Models\CompanyModel;

class AdminCompanies extends BaseController
{
    public function index()
    {
        if (!session()->get('isLoggedIn') || session()->get('user_type') !== 'admin') {
            return redirect()->to('/auth/login');
        }

        $model = new CompanyModel();
        $companies = $model->orderBy('name', 'ASC')->findAll();
        return view('admin/companies_list', [ 'companies' => $companies ]);
    }

    public function create()
    {
        if (!session()->get('isLoggedIn') || session()->get('user_type') !== 'admin') {
            return redirect()->to('/auth/login');
        }
        return view('admin/company_edit', [ 'company' => null ]);
    }

    public function save()
    {
        if (!session()->get('isLoggedIn') || session()->get('user_type') !== 'admin') {
            return redirect()->to('/auth/login');
        }

        $id = (int) ($this->request->getPost('id') ?? 0);
        $data = [
            'name' => trim((string) $this->request->getPost('name')),
            'status' => $this->request->getPost('status') ?: 'active',
        ];

        $model = new CompanyModel();
        if ($id) {
            if (!$model->update($id, $data)) {
                return redirect()->back()->withInput()->with('errors', $model->errors());
            }
        } else {
            if (!$model->insert($data)) {
                return redirect()->back()->withInput()->with('errors', $model->errors());
            }
            $id = (int) $model->getInsertID();
        }

        return redirect()->to('/admin/companies/' . $id . '/form')->with('success', 'Company saved. You can now configure the form fields.');
    }

    public function edit($id)
    {
        if (!session()->get('isLoggedIn') || session()->get('user_type') !== 'admin') {
            return redirect()->to('/auth/login');
        }
        $model = new CompanyModel();
        $company = $model->find($id);
        if (!$company) {
            return redirect()->to('/admin/companies')->with('error', 'Company not found');
        }
        return view('admin/company_edit', [ 'company' => $company ]);
    }

    public function form($id)
    {
        if (!session()->get('isLoggedIn') || session()->get('user_type') !== 'admin') {
            return redirect()->to('/auth/login');
        }
        $model = new CompanyModel();
        $company = $model->find($id);
        if (!$company) {
            return redirect()->to('/admin/companies')->with('error', 'Company not found');
        }
        $schema = $model->getSchemaArray((int) $id);
        return view('admin/company_form_builder', [ 'company' => $company, 'schema' => $schema ]);
    }

    public function saveForm($id)
    {
        if (!session()->get('isLoggedIn') || session()->get('user_type') !== 'admin') {
            return redirect()->to('/auth/login');
        }
        $model = new CompanyModel();
        $company = $model->find($id);
        if (!$company) {
            return redirect()->to('/admin/companies')->with('error', 'Company not found');
        }

        // Expect fields[] arrays posted for keys: key,label,type,required,options,pattern,maxLength,min,max
        $fields = $this->request->getPost('fields');
        if (!is_array($fields)) { $fields = []; }

        // Normalize & validate minimal structure
        $normalized = [];
        foreach ($fields as $f) {
            if (!is_array($f)) continue;
            $key = trim((string)($f['key'] ?? ''));
            $label = trim((string)($f['label'] ?? ''));
            $type = trim((string)($f['type'] ?? 'text'));
            if ($key === '' || $label === '') continue; // skip incomplete
            $entry = [
                'key' => $key,
                'label' => $label,
                'type' => in_array($type, ['text','email','tel','select','number','date','textarea','checkbox']) ? $type : 'text',
                'required' => !empty($f['required']) ? true : false,
            ];
            if (!empty($f['options']) && is_string($f['options'])) {
                $opts = array_values(array_filter(array_map('trim', explode(',', $f['options']))));
                if (!empty($opts)) { $entry['options'] = $opts; }
            }
            if (!empty($f['pattern']) && is_string($f['pattern'])) { $entry['pattern'] = $f['pattern']; }
            if (isset($f['maxLength']) && $f['maxLength'] !== '') { $entry['maxLength'] = (int) $f['maxLength']; }
            if (isset($f['min']) && $f['min'] !== '') { $entry['min'] = (float) $f['min']; }
            if (isset($f['max']) && $f['max'] !== '') { $entry['max'] = (float) $f['max']; }
            $normalized[] = $entry;
        }

        // Enforce unique keys server-side
        $keys = array_map(fn($e) => $e['key'], $normalized);
        if (count($keys) !== count(array_unique($keys))) {
            return redirect()->back()->withInput()->with('error', 'Duplicate field keys detected. Each field key must be unique.');
        }

        $schema = [ 'fields' => $normalized ];
        $ok = $model->update((int)$id, [ 'form_schema' => json_encode($schema) ]);
        if (!$ok) {
            return redirect()->back()->withInput()->with('error', 'Failed to save form schema');
        }

        return redirect()->to('/admin/companies/' . $id . '/form')->with('success', 'Form schema saved');
    }
}
