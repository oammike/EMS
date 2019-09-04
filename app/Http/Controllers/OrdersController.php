<?php

namespace OAMPI_Eval\Http\Controllers;

use Illuminate\Http\Request;

use OAMPI_Eval\Http\Requests;
use OAMPI_Eval\Orders;

class OrdersController extends Controller
{

    protected $pagination_items = 10;

    public function __construct()
    {
      $this->middleware('auth');
      //$this->pagination_items = \Config::get('app.prefs.items_per_page');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $skip = 0 * $this->pagination_items;
      $take = $this->pagination_items;
      
      //$this->layout->contentheader_title = 'Welcome Back!';
      $data = new \stdClass();
      $data->contentheader_title = "Rewards Orders";
      
      $data->orders = Orders::with('customer','item')->orderBy('id', 'asc')->where('status','PENDING')->skip($skip)->take($take)->get( );
      //return view('orders', $data);
      return response()->json( $data );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
