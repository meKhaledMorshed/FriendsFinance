<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Facades\DB;

class AuthorizationController extends Controller
{
    //
    private function activeUserID()
    {
        return session()->get('userID');
    }

    public function pullUserForAuth($filter = null)
    {
        try {

            $data = ['users.id', 'email', 'mobile', 'ccc', 'name', 'birthday', 'gender', 'photo', 'remarks', 'isActive', 'isAuth', 'modifiedDate'];

            if ($filter == 'all') {

                $results = DB::table('users')->join('user_infos', 'users.id', '=', 'user_infos.uid')->latest('modifiedDate')->limit(50)->get($data)->toJson(JSON_PRETTY_PRINT);

                return response($results);
            }

            $searchArray = ['active' => 1, 'inactive' => 0];

            if (array_key_exists($filter, $searchArray)) {

                $value = $searchArray[$filter];

                $results = DB::table('users')->join('user_infos', 'users.id', '=', 'user_infos.uid')->where('isActive', $value)->latest('modifiedDate')->limit(10)->get($data);
                $results = $results->toJson(JSON_PRETTY_PRINT);

                return response($results);
            }

            $searchArray = ['pending' => null, 'authorize' => 1, 'unauthorize' => 0, 'reject' => -1];
            if (array_key_exists($filter, $searchArray)) {

                $value = $searchArray[$filter];

                $results = DB::table('users')->join('user_infos', 'users.id', '=', 'user_infos.uid')->where('isAuth', $value)->latest('modifiedDate')->limit(10)->get($data);
                $results = $results->toJson(JSON_PRETTY_PRINT);

                return response($results);
            }


            if ($filter != null) {

                $results = DB::table('users')->join('user_infos', 'users.id', '=', 'user_infos.uid')
                    ->where('uid', 'like', '%' . $filter . '%')
                    ->orwhere('username', 'like', '%' . $filter . '%')
                    ->orwhere('email', 'like', '%' . $filter . '%')
                    ->orwhere('mobile', 'like', '%' . $filter . '%')
                    ->orwhere('name', 'like', '%' . $filter . '%')
                    ->orwhere('gender', 'like', '%' . $filter . '%')
                    ->orwhere('profession', 'like', '%' . $filter . '%')
                    ->latest('modifiedDate')->limit(10)->get($data);

                $results = $results->toJson(JSON_PRETTY_PRINT);

                return response($results);
            }


            $results = DB::table('users')->join('user_infos', 'users.id', '=', 'user_infos.uid')->where('isAuth', null)->latest('modifiedDate')->limit(10)->get($data);
            $results = $results->toJson(JSON_PRETTY_PRINT);

            return response($results);

            //try end
        } catch (Exception $e) {
            date_default_timezone_set('Asia/Dhaka');
            error_log("Error from pullUserForAuth@AuthorizationController | " . date('d M Y H:i:s', time()) . " | " . $e->getMessage());
            //send response to Requester
            return response('No user found.', 404);
        }
    }

    public function changeUserStatus($change = null, $id = null)
    {
        try {

            if ($id == $this->activeUserID()) {

                return response("Self id modify is not possible.", 403);
            }

            $changeArray = ['active' => 1, 'inactive' => 0];

            if (array_key_exists($change, $changeArray) && $id != null) {

                $value = $changeArray[$change];

                $update = DB::table('users')->join('user_infos', 'users.id', '=', 'user_infos.uid')->where('users.id', $id)
                    ->update(['isActive' => $value, 'modifiedBy' => $this->activeUserID()]);

                $update ?: throw new Exception($change . ' request is not successfull');

                return response($change . ' request is successfull', 201);
            }


            $changeArray = ['unauthorize' => 0, 'reject' => -1, 'pending' => null];

            if (array_key_exists($change, $changeArray) && $id != null) {

                $value = $changeArray[$change];

                $update = DB::table('users')->join('user_infos', 'users.id', '=', 'user_infos.uid')->where('users.id', $id)
                    ->update(['isActive' => 0, 'isAuth' => $value, 'authBy' => null, 'modifiedBy' => $this->activeUserID()]);

                $update ?: throw new Exception($change . ' request is not successfull');

                return response($change . ' request is successfull', 201);
            }

            if ($change == 'authorize' && $id != null) {

                $update = DB::table('users')->join('user_infos', 'users.id', '=', 'user_infos.uid')->where('users.id', $id)
                    ->update(['isActive' => 1, 'isAuth' => 1, 'authBy' => $this->activeUserID()]);

                $update ?: throw new Exception($change . ' request is not successfull');

                return response($change . ' request is successfull', 201);
            }

            return response('Request not successfull', 400);

            //try end
        } catch (Exception $e) {
            date_default_timezone_set('Asia/Dhaka');
            error_log("Error from changeUserStatus@AuthorizationController | " . date('d M Y H:i:s', time()) . " | " . $e->getMessage());
            //send response to Requester
            return response('Request failed.', 304);
        }
    }

    public function pullUserAddress($filter = null)
    {
        try {

            $data = ['user_addresses.id', 'user_addresses.uid', 'name', 'photo', 'house', 'area', 'postOffice', 'policeStation', 'district', 'country', 'user_addresses.remarks', 'type', 'user_addresses.isActive', 'user_addresses.isAuth', 'user_addresses.modifiedDate'];

            if ($filter == 'all') {

                $results =  DB::table('user_addresses')->join('user_infos', 'user_addresses.uid', '=', 'user_infos.uid')
                    ->latest('user_addresses.modifiedDate')->limit(50)->get($data)->toJson(JSON_PRETTY_PRINT);

                return response($results);
            }

            $searchArray = ['active' => 1, 'inactive' => 0];

            if (array_key_exists($filter, $searchArray)) {

                $value = $searchArray[$filter];

                $results =  DB::table('user_addresses')->join('user_infos', 'user_addresses.uid', '=', 'user_infos.uid')
                    ->where('user_addresses.isActive', $value)->latest('user_addresses.modifiedDate')->limit(10)->get($data)->toJson(JSON_PRETTY_PRINT);

                return response($results);
            }

            $searchArray = ['pending' => null, 'authorize' => 1, 'unauthorize' => 0, 'reject' => -1];
            if (array_key_exists($filter, $searchArray)) {

                $value = $searchArray[$filter];

                $results =  DB::table('user_addresses')->join('user_infos', 'user_addresses.uid', '=', 'user_infos.uid')
                    ->where('user_addresses.isAuth', $value)->latest('user_addresses.modifiedDate')->limit(10)->get($data)->toJson(JSON_PRETTY_PRINT);

                return response($results);
            }


            if ($filter != null) {

                $results =  DB::table('user_addresses')->join('user_infos', 'user_addresses.uid', '=', 'user_infos.uid')
                    ->where('user_addresses.uid', 'like', '%' . $filter . '%')
                    ->orwhere('user_infos.name', 'like', '%' . $filter . '%')
                    ->orwhere('house', 'like', '%' . $filter . '%')
                    ->orwhere('area', 'like', '%' . $filter . '%')
                    ->orwhere('postOffice', 'like', '%' . $filter . '%')
                    ->orwhere('policeStation', 'like', '%' . $filter . '%')
                    ->orwhere('district', 'like', '%' . $filter . '%')
                    ->orwhere('country', 'like', '%' . $filter . '%')
                    ->orwhere('type', 'like', '%' . $filter . '%')
                    ->orwhere('user_addresses.remarks', 'like', '%' . $filter . '%')
                    ->latest('modifiedDate')->limit(10)->get($data);

                $results = $results->toJson(JSON_PRETTY_PRINT);

                return response($results);
            }


            $results =  DB::table('user_addresses')->join('user_infos', 'user_addresses.uid', '=', 'user_infos.uid')
                ->where('user_addresses.isAuth', null)->latest('user_addresses.modifiedDate')->limit(10)->get($data)->toJson(JSON_PRETTY_PRINT);

            return response($results);

            //try end
        } catch (Exception $e) {
            date_default_timezone_set('Asia/Dhaka');
            error_log("Error from pullUserAddress@AuthorizationController | " . date('d M Y H:i:s', time()) . " | " . $e->getMessage());
            //send response to Requester
            return response('No data found.', 404);
        }
    }

    // change user's address status  
    public function changeUserAddressStatus($change = null, $id = null)
    {
        try {

            $ckUId = DB::table('user_addresses')->where('id', $id)->where('uid', $this->activeUserID())->count();
            if ($ckUId > 0) {
                return response("Self id modify is not possible.", 403);
            }

            $changeArray = ['active' => 1, 'inactive' => 0];

            if (array_key_exists($change, $changeArray) && $id != null) {

                $value = $changeArray[$change];

                $update = DB::table('user_addresses')->where('id', $id)->update(['isActive' => $value, 'modifiedBy' => $this->activeUserID()]);

                $update ?: throw new Exception($change . ' request is not successfull');

                return response($change . ' request is successfull', 201);
            }

            $changeArray = ['unauthorize' => 0, 'reject' => -1, 'pending' => null];

            if (array_key_exists($change, $changeArray) && $id != null) {

                $value = $changeArray[$change];

                $update = DB::table('user_addresses')->where('id', $id)->update(['isActive' => 0, 'isAuth' => $value, 'authBy' => null, 'modifiedBy' => $this->activeUserID()]);

                $update ?: throw new Exception($change . ' request is not successfull');

                return response($change . ' request is successfull', 201);
            }

            if ($change == 'authorize' && $id != null) {

                $update = DB::table('user_addresses')->where('id', $id)->update(['isActive' => 1, 'isAuth' => 1, 'authBy' => $this->activeUserID()]);

                $update ?: throw new Exception($change . ' request is not successfull');

                return response($change . ' request is successfull', 201);
            }

            return response('Request not successfull', 400);

            //try end
        } catch (Exception $e) {
            date_default_timezone_set('Asia/Dhaka');
            error_log("Error from changeUserAddressStatus@AuthorizationController | " . date('d M Y H:i:s', time()) . " | " . $e->getMessage());
            //send response to Requester
            return response('Request failed.', 304);
        }
    }

    // pull user documents 
    public function pullUserDocuments($filter = null)
    {
        try {
            $data = ['user_documents.id', 'user_documents.uid', 'name', 'docName', 'docNumber', 'document', 'user_documents.remarks', 'user_documents.isActive', 'user_documents.isAuth', 'user_documents.modifiedDate'];

            if ($filter == 'all') {

                $results =  DB::table('user_documents')->join('user_infos', 'user_documents.uid', '=', 'user_infos.uid')
                    ->latest('user_documents.modifiedDate')->limit(50)->get($data)->toJson(JSON_PRETTY_PRINT);

                return response($results);
            }

            $searchArray = ['active' => 1, 'inactive' => 0];

            if (array_key_exists($filter, $searchArray)) {

                $value = $searchArray[$filter];

                $results =  DB::table('user_documents')->join('user_infos', 'user_documents.uid', '=', 'user_infos.uid')
                    ->where('user_documents.isActive', $value)->latest('user_documents.modifiedDate')->limit(9)->get($data)->toJson(JSON_PRETTY_PRINT);

                return response($results);
            }

            $searchArray = ['pending' => null, 'authorize' => 1, 'unauthorize' => 0, 'reject' => -1];
            if (array_key_exists($filter, $searchArray)) {

                $value = $searchArray[$filter];

                $results =  DB::table('user_documents')->join('user_infos', 'user_documents.uid', '=', 'user_infos.uid')
                    ->where('user_documents.isAuth', $value)->latest('user_documents.modifiedDate')->limit(9)->get($data)->toJson(JSON_PRETTY_PRINT);

                return response($results);
            }

            if ($filter != null) {

                $results =  DB::table('user_documents')->join('user_infos', 'user_documents.uid', '=', 'user_infos.uid')
                    ->where('user_documents.uid', 'like', '%' . $filter . '%')
                    ->orwhere('user_infos.name', 'like', '%' . $filter . '%')
                    ->orwhere('docName', 'like', '%' . $filter . '%')
                    ->orwhere('docNumber', 'like', '%' . $filter . '%')
                    ->orwhere('user_documents.remarks', 'like', '%' . $filter . '%')
                    ->latest('modifiedDate')->limit(9)->get($data);

                $results = $results->toJson(JSON_PRETTY_PRINT);

                return response($results);
            }

            $results =  DB::table('user_documents')->join('user_infos', 'user_documents.uid', '=', 'user_infos.uid')
                ->where('user_documents.isAuth', null)->latest('user_documents.modifiedDate')->limit(9)->get($data)->toJson(JSON_PRETTY_PRINT);

            return response($results);

            //try end
        } catch (Exception $e) {
            date_default_timezone_set('Asia/Dhaka');
            error_log("Error from pullUserDocuments@AuthorizationController | " . date('d M Y H:i:s', time()) . " | " . $e->getMessage());
            //send response to Requester
            return response('No data found.', 404);
        }
    }

    // change user's address status  
    public function changeUserDocumentStatus($change = null, $id = null)
    {
        try {

            $ckUId = DB::table('user_documents')->where('id', $id)->where('uid', $this->activeUserID())->count();
            if ($ckUId > 0) {
                return response("Self id modify is not possible.", 403);
            }

            $changeArray = ['active' => 1, 'inactive' => 0];

            if (array_key_exists($change, $changeArray) && $id != null) {

                $value = $changeArray[$change];

                $update = DB::table('user_documents')->where('id', $id)->update(['isActive' => $value, 'modifiedBy' => $this->activeUserID()]);

                $update ?: throw new Exception($change . ' request is not successfull');

                return response($change . ' request is successfull', 201);
            }


            $changeArray = ['unauthorize' => 0, 'reject' => -1, 'pending' => null];

            if (array_key_exists($change, $changeArray) && $id != null) {

                $value = $changeArray[$change];

                $update = DB::table('user_documents')->where('id', $id)->update(['isActive' => 0, 'isAuth' => $value, 'authBy' => null, 'modifiedBy' => $this->activeUserID()]);

                $update ?: throw new Exception($change . ' request is not successfull');

                return response($change . ' request is successfull', 201);
            }

            if ($change == 'authorize' && $id != null) {

                $update = DB::table('user_documents')->where('id', $id)->update(['isActive' => 1, 'isAuth' => 1, 'authBy' => $this->activeUserID()]);

                $update ?: throw new Exception($change . ' request is not successfull');

                return response($change . ' request is successfull', 201);
            }

            return response('Request not successfull', 400);

            //try end
        } catch (Exception $e) {
            date_default_timezone_set('Asia/Dhaka');
            error_log("Error from changeUserDocumentsStatus@AuthorizationController | " . date('d M Y H:i:s', time()) . " | " . $e->getMessage());
            //send response to Requester
            return response('Request failed.', 304);
        }
    }
}
