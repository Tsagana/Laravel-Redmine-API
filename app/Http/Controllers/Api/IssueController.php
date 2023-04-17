<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Issue;
use Illuminate\Http\Request;

class IssueController extends Controller
{
    private $url; 
    private $format;
    private $apiKey; 

    public function __construct() {
        $this->url = config('services.redmine.url') . '/issues' ;
        $this->format = config('services.redmine.format');
        $this->apiKey = config('services.redmine.api_key');
    }

    /**
     * List all issues
     */
    public function index()
    {
        $curl = curl_init($this->url.'.'.$this->format);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    /**
     * Create a new issue
     */
    public function store(Request $request)
    {
        if (!$request->has(['project_id', 'subject', 'status_id', 'priority_id'])) {
            return $this->errorMessage("Required params are missing.");
        }

        $issueData = [
            'issue' => $request->all()
        ];

        $curl = curl_init($this->url.'.'.$this->format);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($issueData));
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'X-Redmine-API-Key: '. $this->apiKey
        ]);
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }


    /**
     * Update the issue
     */
    public function update(Request $request)
    {
        if (!$request->filled('task_id')) {
            return $this->errorMessage("Task id is missing");
        }

        $issueData = [
            'issue' => $request->all()
        ];

        $curl = curl_init($this->url.'/'.$request->input('task_id').'.'.$this->format);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($issueData));
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'X-Redmine-API-Key: '. $this->apiKey
        ]);
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;            
    }

    /**
     * Remove the issue
     */
    public function destroy(Request $request)
    {   
        if (!$request->filled('task_id')) {
            return $this->errorMessage("Task id is missing");
        }

        $curl = curl_init($this->url.'/'.$request->input('task_id').'.'.$this->format);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'X-Redmine-API-Key: '. $this->apiKey
        ]);
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    /**
     * Returns error message in json
     */
    public function errorMessage($msg) {
        return json_encode(["error" => $msg]);
    }
}
