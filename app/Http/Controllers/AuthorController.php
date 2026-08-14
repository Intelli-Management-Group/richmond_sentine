<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AuthorDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Validator;

class AuthorController extends Controller
{
    public function loadAuthor(Request $request)
    {
        return view('author/list');
    }

    /**
     *  Method for pagination in category list
     *
     *  @return \Illuminate\Http\JsonResponse
     */
    public function authorPaginationData(Request $request)
    {
        try {
            $start = \Request::input('start');
            $end = \Request::input('length');
            $search_value = \Request::input('search')['value'];
            $search_value = strtolower($search_value);


            $author_data_array = array();
            $author_data = AuthorDetail::whereRaw("1");
            if ($search_value != "") {
                $author_data = $author_data->whereRaw("(name like'%" . $search_value . "%')");
            }
            $total_author = $author_data->count();
            $author_data = $author_data->skip($start)->take($end)->get();

            foreach (count((array)$author_data) > 0 ? $author_data : array() as $a) {
                $publish_button = "";
                if ($a->status == 1) {
                    $onclick = "window.location='" . url("unpublish-author" . "/" . $a->id) . "'";
                    $publish_button = '<span style="cursor:default"><input type="submit" onclick="' . $onclick . '" class="publish_button" value="Deactivate"/></span>';
                }else if ($a->status == 0) {
                    $onclick = "window.location='" . url("publish-author" . "/" . $a->id) . "'";
                    $publish_button = '<span style="cursor:default"><input type="submit" onclick="' . $onclick . '" class="publish_button" value="Activate"/></span>';
                }
                $delete_onclick = 'javascript:deleteAuthor(' . $a->id . ');';
                $author_image = getAuthorImage($a->profileImage);
                array_push($author_data_array, array(
                    'checkbox' => "<input type='checkbox' value='" . $a->id . "' class='delete_entity'/>",
                    'name' => $a->name,
                    'email' => $a->email,
                    'twitterHandle' => $a->twitterHandle,
                    'facebookHandle' => $a->facebookHandle,
                    // 'profileImage' => "<div class='outerprofile'><img src='" . getAuthorImage($a->profileImage) . "' class='author_image'></div>",
                    'profileImage' => "<div class=\"outerprofile\"><img src=\"$author_image\" class=\"author_image\"></div>",
                    'action' => '<span class="editicn"><a href="' . url("/edit-author/$a->id") . '"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a></span>
                     <span class="deleteicn"><a onclick="' . $delete_onclick . '"><i class="fa fa-trash" aria-hidden="true"></i></a></span>' . $publish_button,

                ));
            }

            return json_encode(array(
                "draw" => isset($request['draw']) ? intval($request['draw']) : 0,
                "data" => $author_data_array,
                "recordsTotal" => $total_author,
                "recordsFiltered" => $total_author,
            ));
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    public function editAuthor($id)
    {
        if ($id == "") {
            return view("errors.403");
        }
        $data = AuthorDetail::where("id", "$id")->first();
        if (!$data) {
            return view("errors.404");
        }
        return view('author/insert', compact('data', 'title'));
    }
    public function updateAuthor(Request $request)
    {
        try {
            $id = request("id");
            if ($id == "") {
                return view("errors.403");
            }
            $data = AuthorDetail::where("id", "$id")->first();
            if (!$data) {
                return view("errors.404");
            }
            $element_array = array(
                'name' => 'required',
            );
            $image = $request->file("profileImage");
            if (isset($image)) {
                $element_array['profileImage'] = 'image|mimes:jpeg,png,jpg';
            }
            $validator = Validator::make($request->all(), $element_array, [
                'name.required' => 'Please enter your name',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $data->name = $request->input("name");
            $data->email = $request->input("email");
            $data->twitterHandle = $request->input("twitterHandle");
            $data->facebookHandle = $request->input("facebookHandle");
            $data->position = $request->input("position");
            $data->bio = $request->input("bio");
            $data->status =($request->input("status") != null ? $request->input("status") : 0);
            $data->save();
            if (isset($image)) {
                $image_name = $data->id . "." . $image->getClientOriginalExtension();
                $destination_path = public_path("uploads/team");

                if (!File::exists($destination_path)) {
                    File::makeDirectory($destination_path);
                }
                $image->move($destination_path, $image_name);
                $data->profileImage = $image_name;
                $data->save();
            }
            return redirect()->route("author-list")->with("success", "Author updated successfully ");

        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function deleteAuthor($id)
    {
        try {
            if ($id == "") {
                return view("errors.403");
            }
            $data = AuthorDetail::find($id);
            if (!$data) {
                return view("errors.404");
            }
            if ($data->profileImage != null && file_exists(public_path("uploads/team/" . $data->profileImage))) {
                unlink(public_path("uploads/team/" . $data->profileImage));
            }
            $data->delete();
            return redirect()->route("author-list")->with("success", "Author deleted successfully");
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     *  Method to delete authors
     *
     *  @return \Illuminate\Http\JsonResponse
     */
    public function deleteAuthors()
    {
        try {
            $ids_to_delete = request("author_ids");
            if ($ids_to_delete == "") {
                return view("errors.403");
            }
            $author_ids = explode(",", $ids_to_delete);
            foreach (count($author_ids) > 0 ? $author_ids : array() as $a) {
                $data = AuthorDetail::find($a);
                if ($data) {
                    if ($data->profileImage != null && file_exists(public_path("uploads/team/" . $data->profileImage))) {
                        unlink(public_path("uploads/team/" . $data->profileImage));
                    }
                    $data->delete();
                }
            }
            return "success";
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function loadAddauthor(Request $request)
    {
        return view('author/insert');
    }

    public function insertAuthor(Request $request)
    {
        try {
            $element_array = array(
                'name' => 'required',
            );
            $image = $request->file("profileImage");
            if (isset($image)) {
                $element_array['profileImage'] = 'image|mimes:jpeg,png,jpg';
            }
            $validator = Validator::make($request->all(), $element_array, [
                'name.required' => 'Please enter your name',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $data = new AuthorDetail();
            $data->name = $request->input("name");
            $data->email = $request->input("email");
            $data->twitterHandle = $request->input("twitterHandle");
            $data->facebookHandle = $request->input("facebookHandle");
            $data->position = $request->input("position");
            $data->bio = $request->input("bio");
            $data->status = ($request->input("status") != null ? $request->input("status") : 0);
            $data->save();

            if (isset($image)) {
                $image_name = $data->id . "." . $image->getClientOriginalExtension();
                $destination_path = public_path("uploads/team");

                if (!File::exists($destination_path)) {
                    File::makeDirectory($destination_path);
                }
                $image->move($destination_path, $image_name);
                $data->profileImage = $image_name;
                $data->save();
            }
            return redirect()->route("author-list")->with("success", "Author inserted successfully");

        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function unpublishAuthor($id)
    {
        try {
            if ($id == "") {
                return view("errors.403");
            }
            $data = AuthorDetail::find($id);
            if (!$data) {
                return view("errors.404");
            }
            $data->status = 0;
            $data->save();
            return redirect()->route("author-list")->with("success", "Author deactivated successfully");
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    public function publishAuthor($id)
    {
        try {
            if ($id == "") {
                return view("errors.403");
            }
            $data = AuthorDetail::find($id);
            if (!$data) {
                return view("errors.404");
            }
            $data->status = 1;
            $data->save();
            return redirect()->route("author-list")->with("success", "Author activated successfully");
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
