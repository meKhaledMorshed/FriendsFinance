<?php

namespace App\Http\Controllers;

use App\Models\Select_option;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SelectOptionController extends Controller
{
    //

    private function datalistKey()
    {
        $keys = ['key', 'parent', 'group', 'value', 'id-name-value', 'detail'];
        return $keys;
    }

    public function datalist($key = null, $parent = null, $group = null)
    {
        try {
            // check keys 
            if ($key != null && !in_array($key, $this->datalistKey())) {
                return response('Invalied key.', 404);
            }

            if (($key == 'value' || $key == 'id-name-value' || $key == 'detail') && $parent == null && $group == null) {
                return response('second parameter is missing.', 404);
            }

            if ($key == 'key' && $parent == null && $group == null) {
                $keys = $this->datalistKey();
                return response(json_encode($keys, JSON_PRETTY_PRINT));
            }

            if ($key == 'group' && $parent == null && $group == null) {
                $results = DB::table('select_options')->where('isAuth', 1)->where('type', 'Group')->orderBy('optionName', 'asc')->pluck('group');
                return $results->count() > 0  ? response($results->toJson(JSON_PRETTY_PRINT)) : response('No result found', 404);
            }

            if ($key == 'parent' && $parent == null && $group == null) {
                $results = DB::table('select_options')->where('isAuth', 1)->where('parentID', '!=', null)->orderBy('parentValue', 'asc')
                    ->get(['parentValue'])->unique()->pluck('parentValue');
                return $results->count() > 0  ? response($results->toJson(JSON_PRETTY_PRINT)) : response('No result found', 404);
            }

            if ($key == 'value' && $parent != null && $group != null) {
                $results = DB::table('select_options')->where('isAuth', 1)->where('type', 'option')->where('group', $group)
                    ->where('parentValue', $parent)->orderBy('optionValue', 'asc')->get(['optionValue'])->unique()->pluck('optionValue');
                return $results->count() > 0  ? response($results->toJson(JSON_PRETTY_PRINT)) : response('No result found', 404);
            }

            if ($key == 'value' && $parent != null && $group == null) {
                $results = DB::table('select_options')->where('isAuth', 1)->where('type', 'option')->where('parentID', $parent)->orWhere('parentValue', $parent)
                    ->orderBy('optionValue', 'asc')->get(['optionValue'])->unique()->pluck('optionValue');
                return $results->count() > 0  ? response($results->toJson(JSON_PRETTY_PRINT)) : response('No result found', 404);
            }

            if ($key == 'id-name-value' && $parent != null && $group != null) {
                $results = DB::table('select_options')->where('isAuth', 1)->where('type', 'option')->where('parentValue', $parent)->where('group', $group)
                    ->orderBy('optionValue', 'asc')->get(['id', 'optionName', 'optionValue']);
                return $results->count() > 0  ? response($results->toJson(JSON_PRETTY_PRINT)) : response('No result found', 404);
            }
            if ($key == 'id-name-value' && $parent != null && $group == null) {
                $results = DB::table('select_options')->where('isAuth', 1)->where('type', 'option')->where('parentValue', $parent)
                    ->orderBy('optionValue', 'asc')->get(['id', 'optionName', 'optionValue']);
                return $results->count() > 0  ? response($results->toJson(JSON_PRETTY_PRINT)) : response('No result found', 404);
            }

            if ($key == 'detail' && $parent != null && $group != null) {
                $results = Select_option::where('isAuth', 1)->where('parentValue', $parent)->where('group', $group)->orderBy('optionValue', 'asc')->get();
                return $results->count() > 0  ? response($results->toJson(JSON_PRETTY_PRINT)) : response('No result found', 404);
            }
            if ($key == 'detail' && $parent != null && $group == null) {
                $results = Select_option::where('isAuth', 1)->where('parentValue', $parent)->orderBy('optionValue', 'asc')->get();
                return $results->count() > 0  ? response($results->toJson(JSON_PRETTY_PRINT)) : response('No result found', 404);
            }

            $result = Select_option::all();
            return $result->count() > 0  ? response($result->toJson(JSON_PRETTY_PRINT)) : response('No result found', 404);

            //try end
        } catch (Exception $e) {
            date_default_timezone_set('Asia/Dhaka');
            error_log("Error from datalist@ApiController | " . date('d M Y H:i:s', time()) . " | " . $e->getMessage());
            return response('No data found.', 404);
        }
    }

    public function selectOption($filter = null)
    {
        try {

            if ($filter == 'all') {
                $results = Select_option::all()->toJson(JSON_PRETTY_PRINT);
                return $results;
            }
            if ($filter == 'active') {
                $results = Select_option::where('isActive', 1)->latest('updated_at')->limit(7)->orderBy('optionName', 'asc')->get()->toJson(JSON_PRETTY_PRINT);
                return $results;
            }
            if ($filter == 'inactive') {
                $results = Select_option::where('isActive', 0)->latest('updated_at')->limit(7)->orderBy('optionName', 'asc')->get()->toJson(JSON_PRETTY_PRINT);
                return $results;
            }
            if ($filter === 'group' || $filter === 'Group') {
                $results = Select_option::where('type', $filter)->latest('updated_at')->orderBy('type', 'asc')->get()->toJson(JSON_PRETTY_PRINT);
                return $results;
            }

            $searchArray = ['pending' => null, 'auth' => 1, 'unauth' => 0, 'reject' => -1];
            if (array_key_exists($filter, $searchArray)) {

                $searchValue = $searchArray[$filter];

                $results = Select_option::where('isAuth',  $searchValue)
                    ->latest('updated_at')->orderBy('optionName', 'asc')->limit(7)->get();

                $results = $results->toJson(JSON_PRETTY_PRINT);

                return $results;
            }


            if ($filter != null) {

                $results = Select_option::where('isAuth', 1)
                    ->where('id', 'like', '%' . $filter . '%')
                    ->orwhere('optionName', 'like', '%' . $filter . '%')
                    ->orwhere('optionValue', 'like', '%' . $filter . '%')
                    ->orwhere('group', 'like', '%' . $filter . '%')
                    ->orwhere('remarks', 'like', '%' . $filter . '%')
                    ->latest('updated_at')->limit(10)->get();

                $results = $results->toJson(JSON_PRETTY_PRINT);

                return $results;
            }

            $results = Select_option::where('isAuth', 1)->latest('id')->limit(10)->get()->toJson(JSON_PRETTY_PRINT);
            return $results;

            //try end here & catch start
        } catch (Exception $e) {
            date_default_timezone_set('Asia/Dhaka');
            error_log("Error from selectOption@SelectOptionController | " . date('d M Y H:i:s', time()) . " | " . $e->getMessage());
            return response($e->getMessage(), 404);
        }
    }

    public function singleSelectOption($id)
    {
        try {
            $results = Select_option::find($id);

            return $results->toJson(JSON_PRETTY_PRINT);
            //
        } catch (Exception $e) {
            return 'failed ' . $e->getMessage();
        }
    }

    public function selectOptionForParent($parent = null, $group = null)
    {
        try {

            if ($parent != null && $group != null) {

                $result = Select_option::where('type', 'Option')
                    ->where('group', $group)
                    ->where('parentValue', $parent)
                    ->where('isActive', 1)->where('isAuth', 1)
                    ->orderBy('optionName', 'asc')
                    ->get(['optionName', 'optionValue']);

                return $result->count() > 0  ? response($result->toJson(JSON_PRETTY_PRINT)) : response('No result found', 302);
            }
            return response("Required paramiters can't be null", 400);
        } catch (Exception $e) {
            date_default_timezone_set('Asia/Dhaka');
            error_log("Error from selectOptionForParent@SelectOptionController | " . date('d M Y H:i:s', time()) . " | " . $e->getMessage());
            return response('Unable to check', 500);
        }
    }

    public function optionsGroups()
    {
        $groups = Select_option::where('type', 'Group')->get('group')->unique('group');
        // remove key 
        foreach ($groups as   $single) {

            $group[] = $single->group;
        }
        // convert to json
        $group = json_encode($group);

        return $group;
    }
    // returns an options data based on id 
    public function optionsParentValue($id = null)
    {
        try {
            $value = Select_option::where('id', $id)->first(['optionName', 'optionValue', 'group']);

            return $value ?: response('No result found.', 404);
            //try end
        } catch (Exception $e) {
            return response('No result found', 404);
        }
    }

    // insert or update a new option 
    public function addSelectOption(Request $request)
    {
        $request->validate([
            'id'          => 'exists:select_options,id|nullable',
            'optName'     => 'Required',
            'optValue'    => 'Required',
            'parentID'    => 'exists:select_options,id|nullable',
            'optGroup'    => 'exists:select_options,group|nullable',
            'optType'     => 'Required',
            'optRemarks'  => 'string|nullable',
            'isActive'    => 'boolean|nullable'
        ]);

        try {

            $existing = DB::table('select_options')->where('optionValue', $request->optValue)->where('parentID', $request->parentID)->where('group', $request->optGroup)->count();
            if ($existing > 0) {
                return response('This data already available.', status: 406);
            }

            $uid = session()->get('userID');

            $data = [
                'optionName'  => ucwords($request->optName),
                'optionValue' => $request->optValue,
                'parentID'    => $request->parentID,
                'parentValue' => $request->parentID != null ? $this->optionsParentValue($request->parentID)->optionValue : null,
                'group'       => $request->optType == 'Option' ? $request->optGroup : str_replace(' ', '-', strtolower($request->optName)),
                'type'        => $request->optType,
                'remarks'     => $request->optRemarks,
                'isActive'    => $request->isActive,
                'insertedBy'  => $uid,
                'isAuth'      => null,
                'authBy'      => null
            ];

            if ($request->id == null) {
                $save = DB::table('select_options')->insert($data);
                $save ?: throw new Exception('Data not inserted.');
                return response('Inserted successfully', status: 201);
            }

            $update = DB::table('select_options')->where('id', $request->id)->update($data);
            $update ?: throw new Exception('Data not updated.');
            return response('Updated successfully', status: 201);
            //try end
        } catch (Exception $e) {
            date_default_timezone_set('Asia/Dhaka');
            error_log("Error from addSelectOption@SelectOptionController | " . date('d M Y H:i:s', time()) . " | " . $e->getMessage());
            return response('Request not successfully', status: 406);
        }
    }

    // for changing status of select options 
    public function changeStatusSO($request = null, $id = null)
    {
        try {
            $reqArray = ['active', 'inactive', 'auth', 'unauth', 'reject'];
            if (in_array($request, $reqArray) && $id == 'all') {

                // for active any single select option 
                if ($request == 'active') {
                    $data = ['isActive' => 1];
                    $update = DB::table('select_options')->update($data);
                    $update ?: throw new Exception('All option activeted.');
                    return response('All option activeted.', 202);
                }

                // for inactive any single select option 
                if ($request == 'inactive') {
                    $data = ['isActive' => 0];
                    $update = DB::table('select_options')->update($data);
                    $update ?: throw new Exception('All option Deactivated.');
                    return response('All option Deactivated.', 202);
                }

                // for Authorized any single select option 
                if ($request == 'auth') {
                    $data = ['isActive' => 1, 'isAuth' => 1, 'authBy' => session()->get('userID')];
                    $update = DB::table('select_options')->update($data);
                    $update ?: throw new Exception('All option Authorized.');
                    return response('All option Authorized.', 202);
                }

                // for Unauthorized any single select option 
                if ($request == 'unauth') {
                    $data = ['isActive' => 0, 'isAuth' => 0];
                    $update = DB::table('select_options')->update($data);
                    $update ?: throw new Exception('All option Unauthorized.');
                    return response('All option Unuthorized.', 202);
                }

                // for reject any single select option 
                if ($request == 'reject') {
                    $data = ['isActive' => 0, 'isAuth' => -1];
                    $update = DB::table('select_options')->update($data);
                    $update ?: throw new Exception('All option Rejected.');
                    return response('All option Rejected.', 202);
                }
            }
            if (in_array($request, $reqArray) && ($id != 'all' or $id != null)) {

                // for active any single select option 
                if ($request == 'active') {
                    $data = ['isActive' => 1];
                    $update = DB::table('select_options')->where('id', $id)->update($data);
                    $update ?: throw new Exception('Not activeted.');
                    return response('Option activeted.', 202);
                }

                // for inactive any single select option 
                if ($request == 'inactive') {
                    $data = ['isActive' => 0];
                    $update = DB::table('select_options')->where('id', $id)->update($data);
                    $update ?: throw new Exception('Not Deactivated.');
                    return response('Option Deactivated.', 202);
                }

                // for Authorized any single select option 
                if ($request == 'auth') {
                    $data = ['isActive' => 1, 'isAuth' => 1];
                    $update = DB::table('select_options')->where('id', $id)->update($data);
                    $update ?: throw new Exception('Not Authorized.');
                    return response('Option Authorized.', 202);
                }

                // for Unauthorized any single select option 
                if ($request == 'unauth') {
                    $data = ['isActive' => 0, 'isAuth' => 0];
                    $update = DB::table('select_options')->where('id', $id)->update($data);
                    $update ?: throw new Exception('Not Unauthorized.');
                    return response('Option Unuthorized.', 202);
                }

                // for reject any single select option 
                if ($request == 'reject') {
                    $data = ['isActive' => 0, 'isAuth' => -1];
                    $update = DB::table('select_options')->where('id', $id)->update($data);
                    $update ?: throw new Exception('Not Rejected.');
                    return response('Option Rejected.', 202);
                }




                //
            }

            // in case of o condition match 
            throw new Exception('Condition not match.');

            //try end here
        } catch (Exception $e) {
            date_default_timezone_set('Asia/Dhaka');
            error_log("Error from changeStatusSO@SelectOptionController | " . date('d M Y H:i:s', time()) . " | " . $e->getMessage());
            return response($e->getMessage(), 304);
        }
    }

    // request-on-select_ptions-table
    public function requestOnOSTable($request = null)
    {
        try {
            // throw exception if paramiter is null
            $request == null ? throw new Exception("Parameter can't be empty.") : '';

            // to make a backup json file
            if ($request == 'makeBackup') {

                $result = Select_option::all()->toJson(JSON_PRETTY_PRINT);

                $save = file_put_contents('assets/json/backup_Select_options.json', $result);

                return $save ? response('Data successfully backup as JSON.', 201) : response('Data not saved.', 500);
            }

            // use select options from backup file 
            if ($request == 'useBackup') {

                $deleteExisting = Select_option::truncate();
                $deleteExisting ?? throw new Exception('Existing data not deleted.');

                $file = file_exists('assets/json/backup_Select_options.json');
                $file ?? throw new Exception('No back file found.');

                $options = file_get_contents('assets/json/backup_Select_options.json');
                $options = json_decode($options, true);

                foreach ($options as $option) {
                    DB::table('select_options')->insert($option);
                }

                return  response('Data successfully retrive from  backup.', 201);
            }

            // use select options from default json file
            if ($request == 'useDefault') {

                $deleteExisting = Select_option::truncate();
                $deleteExisting ?? throw new Exception('Existing data not deleted.');

                $file = file_exists('assets/json/Select_options.json');
                $file ?? throw new Exception('No back file found.');

                $options = file_get_contents('assets/json/Select_options.json');
                $options = json_decode($options, true);

                foreach ($options as $option) {
                    DB::table('select_options')->insert($option);
                }

                return  response('Data successfully retrive from  default select_options.', 201);
            }

            // delete all data from slect_options table in DB
            if ($request == 'deleteAll') {

                $deleteExisting = Select_option::truncate();
                $deleteExisting ?? throw new Exception('Existing data not deleted.');

                return  response('All data successfully deleted.', 201);
            }
        } catch (Exception $e) {
            date_default_timezone_set('Asia/Dhaka');
            error_log("Error from requestOnOSTable@SelectOptionController | " . date('d M Y H:i:s', time()) . " | " . $e->getMessage());
            return response($e->getMessage(), status: 406);
        }
    }
}
