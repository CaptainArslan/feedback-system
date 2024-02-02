<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
            $form_fields  = [
                'client_id' => [
                    'type' => 'text',
                    'name' => 'crm_client_id',
                    'label' => 'CRM Client Id',
                    'value' => supersetting('crm_client_id'),
                    'placeholder' => 'Client Client Id',
                    'required' => false,
                    'col' => 6,
                    'extra' => ''
                ],

                'client_secret' => [
                    'type' => 'text',
                    'name' => 'crm_client_secret',
                    'label' => 'CRM Client Secret',
                    'value' => supersetting('crm_client_secret'),
                    'placeholder' => 'CRM Client Secret',
                    'required' => false,
                    'col' => 6,
                    'extra' => ''
                ],
                'company_logo' => [
                    'type' => 'file',
                    'name' => 'company_logo',
                    'label' => 'Company Logo',
                    'value' => setting('company_logo'),
                    'placeholder' => 'Company Logo',
                    'required' => true,
                    'col' => 3,
                    'extra' => ''
                ],

            ];
        return view('settings.setting', compact('form_fields'));
    }

    public function save(Request $request)
    {
        foreach ($request->except('_token') as $key => $value) {
            if($request->hasFile($key)){
                $value = uploadFile($request->file($key), 'uploads/logos', $key.'_'.time());
            }
            save_settings($key, $value);
        }
        return redirect()->back()->with('success', 'Settings saved successfully');
    }

    //goHighLevel oAuth 2.0 callback
    public function goHighLevelCallback(Request $request)
    {
        return ghl_token($request->code ?? '');
    }
}
