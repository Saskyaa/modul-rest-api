<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{
    /**
     * Format global response
     */
    private function response($success, $message, $data = [], $status = 200)
    {
        return response()->json([
            "success" => $success,
            "message" => $message,
            "data" => $data
        ], $status);
    }

    /**
     * GET /api/books
     * With pagination validation
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'paginate' => 'integer|min:1|max:10'
        ]);

        if ($validator->fails()) {
            return $this->response(false, "Validation error", [
                "errors" => $validator->errors()
            ], 422);
        }

        $paginate = $request->paginate ?? 5;

        $books = Book::paginate($paginate);

        return $this->response(true, "Data retrieved successfully", $books);
    }


    /**
     * GET /api/books/search?title=keyword
     */
    public function search(Request $request)
    {
        if (!$request->has("title")) {
            return $this->response(false, "Title parameter is required", []);
        }

        $books = Book::where("title", "like", "%".$request->title."%")->get();

        return $this->response(true, "Search results retrieved successfully", $books);
    }


    /**
     * GET /api/books/filter/year?year=xxxx
     */
    public function filterByYear(Request $request)
    {
        if (!$request->has("year")) {
            return $this->response(false, "Year parameter is required", []);
        }

        $books = Book::where("year", $request->year)->get();

        return $this->response(true, "Books filtered by year successfully", $books);
    }


    /**
     * GET /api/books/filter?publisher=xxx&author=xxx
     */
    public function filterByPublisherAndAuthor(Request $request)
    {
        if (!$request->publisher && !$request->author) {
            return $this->response(false, "At least one parameter (publisher or author) is required");
        }

        $query = Book::query();

        if ($request->publisher) {
            $query->where("publisher", $request->publisher);
        }

        if ($request->author) {
            $query->where("author", $request->author);
        }

        return $this->response(true, "Books filtered by publisher and/or author", $query->get());
    }


    /**
     * GET /api/books/range?start=1990&end=2000
     */
    public function filterByYearRange(Request $request)
    {
        if (!$request->start || !$request->end) {
            return $this->response(false, "Both start and end parameters are required");
        }

        $books = Book::whereBetween("year", [$request->start, $request->end])->get();

        return $this->response(true, "Books filtered by year range", $books);
    }


    /**
     * GET /api/books/sort?order=desc
     */
    public function sortByYear(Request $request)
    {
        $order = $request->order ?? "asc";

        if (!in_array($order, ["asc", "desc"])) {
            return $this->response(false, "Order must be 'asc' or 'desc'");
        }

        $books = Book::orderBy("year", $order)->get();

        return $this->response(true, "Books sorted by year ($order)", $books);
    }
}