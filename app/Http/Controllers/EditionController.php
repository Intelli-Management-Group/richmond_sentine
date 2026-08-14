<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\DigitalEditionDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManagerStatic as Image;


class EditionController extends Controller
{
    /**
     *  Method to edition list page
     *
     *  @return \Illuminate\Http\JsonResponse
     */

    public function loadEditions(Request $request)
    {
        try {
            return view("editions.list");
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     *  Method for pagination in edition list
     *
     *  @return \Illuminate\Http\JsonResponse
     */
    public function editionPaginationData(Request $request)
    {
        try {
            $start = \Request::input('start');
            $end = \Request::input('length');

            $search_value = \Request::input('search')['value'];
            $search_value = strtolower($search_value);

            $order_detail = \Request::input('order');
            $sort_order = "DESC";
            if (is_array($order_detail) && count($order_detail) > 0) {
                $sort_order = strtoupper($order_detail[0]['dir']);
            }
            $edition_data = DigitalEditionDetail::orderBy("publishDate", $sort_order);
            if ($search_value != "") {
                $edition_data = $edition_data->whereRaw("(volumeEdition like '%" . $search_value . "%' OR name like '%" . $search_value . "%' OR editionNumber like '%" . $search_value . "%')");
            }
            $total_editions = $edition_data->count();
            $edition_data = $edition_data->skip($start)->take($end)->get();

            $edition_data_array = array();
            foreach (count((array)$edition_data) > 0 ? $edition_data : array() as $e) {
                $image_name = ($e->thumbnailImage != "" ? ("../uploads/editions/" . $e->thumbnailImage) : "../images/image-not-found.png");
                array_push($edition_data_array, array(
                    "checkbox" => "<input type='checkbox' value='" . $e->id . "' class='delete_entity'/>",
                    "name" => $e->name,
                    "volume" => $e->volumeEdition,
                    "edition_number" => $e->editionNumber,
                    "publish_date" => Carbon::parse($e->publishDate)->format("Y-m-d H:i:s"),
                    "edition_date" => $e->editionDate,
                    "image" => "<div class='text-center'><img src='" . $image_name . "' class='edition_image' alt='$e->name'/></div>",
                    "action" => '<span class="editicn"><a href="' . (url("edit-edition") . "/" . $e->id) . '"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a></span><span class="deleteicn"><a onclick="javascript:deleteEdition(' . $e->id . ')"><i class="fa fa-trash" aria-hidden="true"></i></a></span>',
                ));
            }
            return json_encode(array(
                "draw" => isset($request['draw']) ? intval($request['draw']) : 0,
                "data" => $edition_data_array,
                "recordsTotal" => $total_editions,
                "recordsFiltered" => $total_editions,
            ));
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    /**
     *  Method to load add edition view
     *
     *  @return \Illuminate\Http\JsonResponse
     */

    public function loadAddEdition()
    {
        try {
            return view("editions.add");
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    /**
     *  Method to insert edition data
     *
     *  @return \Illuminate\Http\JsonResponse
     */

    public function insertEdition(Request $request)
    {
        try {
            $edition_id = $request->input("edition_id");
            $element_array = array(
                'name' => 'required',
                'volumeEdition' => 'required',
                'editionNumber' => 'required',
                'editionDate' => 'required'
            );
            if ($edition_id == null) {
                $element_array['pdfFile'] = 'required';
            }
            $validator = Validator::make($request->all(), $element_array, [
                'name.required' => 'Please enter name',
                'volumeEdition.required' => 'Please enter volume number',
                'editionNumber.required' => 'Please enter edition number',
                'editionDate.required' => 'Please select date',
                'pdfFile.required' => 'Please select edition PDF'
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            if ($edition_id == null) {
                $data = new DigitalEditionDetail();
            } else {
                $data = DigitalEditionDetail::find($edition_id);
            }
            $data->name = $request->input("name");
            if ($edition_id == null) {
                $data->publishDate = Carbon::now()->format("Y-m-d H:i:s");
                $data->createdBy = Auth::user()->id;
            }
            $data->volumeEdition = $request->input("volumeEdition");
            $data->editionNumber = $request->input("editionNumber");
            $data->editionDate = $request->input("editionDate");
            $data->updatedBy = Auth::user()->id;
            $data->save();

            $destination_path = public_path("uploads/editions");
            if (!File::exists($destination_path)) {
                File::makeDirectory($destination_path);
            }
            $pdf_file = $request->file("pdfFile");
            if (isset($pdf_file)) {
                $pdf_file_name = $data->id . "." . $pdf_file->getClientOriginalExtension();
                $pdf_file->move($destination_path, $pdf_file_name);
                $data->pdfFile = $pdf_file_name;

                $pathToPdf = $destination_path . "/" . $pdf_file_name;
                $thumbnail_name = ($data->id . ".png");
                $output = $response = "";

                exec('gs -sDEVICE=jpeg -dFirstPage=1 -dLastPage=1 -o '.($destination_path . "/" . $thumbnail_name).' '.$pathToPdf);

                $data->thumbnailImage = $thumbnail_name;
                $data->save();
            }
            return redirect()->route("edition-list")->with("success", "Edition " . ($edition_id == null ? "added" : "updated") . " successfully");
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     *  Method for edit edition
     *
     *  @return \Illuminate\Http\JsonResponse
     */

    public function editEdition($id)
    {
        try {
            if ($id == "") {
                return view("errors.403");
            }
            $edition_data = DigitalEditionDetail::where("id", $id)->first();
            if (!$edition_data) {
                return view("errors.404");
            }
            return view("editions.add", compact("edition_data"));
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     *  Method to delete edition
     *
     *  @return \Illuminate\Http\JsonResponse
     */
    public function deleteEdition($id)
    {
        try {
            if ($id == "") {
                return view("errors.403");
            }
            $edition_data = DigitalEditionDetail::find($id);
            if (!$edition_data) {
                return view("errors.404");
            }
            if ($edition_data->pdfFile != null && file_exists(public_path("uploads/editions/" . $edition_data->pdfFile))) {
                unlink(public_path("uploads/editions/" . $edition_data->pdfFile));
            }
            if ($edition_data->thumbnailImage != null && file_exists(public_path("uploads/editions/" . $edition_data->thumbnailImage))) {
                unlink(public_path("uploads/editions/" . $edition_data->thumbnailImage));
            }
            $edition_data->delete();
            return redirect()->route("edition-list")->with("success", "Edition deleted successfully");
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     *  Method to delete editions
     *
     *  @return \Illuminate\Http\JsonResponse
     */
    public function deleteEditions()
    {
        try {
            $ids_to_delete = request("edition_ids");
            if ($ids_to_delete == "") {
                return view("errors.403");
            }
            $edition_ids = explode(",", $ids_to_delete);
            foreach (count($edition_ids) > 0 ? $edition_ids : array() as $e) {
                $edition_data = DigitalEditionDetail::find($e);
                if ($edition_data) {
                    if ($edition_data->pdfFile != null && file_exists(public_path("uploads/editions/" . $edition_data->pdfFile))) {
                        unlink(public_path("uploads/editions/" . $edition_data->pdfFile));
                    }
                    if ($edition_data->thumbnailImage != null && file_exists(public_path("uploads/editions/" . $edition_data->thumbnailImage))) {
                        unlink(public_path("uploads/editions/" . $edition_data->thumbnailImage));
                    }
                    $edition_data->delete();
                }
            }
            return "success";
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
