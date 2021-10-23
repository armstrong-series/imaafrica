@extends('dashboard.layout')

@section('content')
<!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h4>
           {{ trans('admin.admin') }} <i class="fa fa-angle-right margin-separator"></i> {{ trans('misc.deposits_history') }} ({{$data->total()}})
          </h4>

        </section>

        <!-- Main content -->
        <section class="content">

        	<div class="row">
            <div class="col-xs-12">
              <div class="box">
                <div class="box-header">
                  <h3 class="box-title">
                  		<strong>{{trans('misc.funds')}}</strong>: {{\App\Helper::amountFormatDecimal(Auth::user()->funds)}}
                      <a href="{{url('user/dashboard/add/funds')}}" class="btn btn-sm btn-success no-shadow">
                        <i class="glyphicon glyphicon-plus myicon-right"></i> {{trans('misc.add_funds')}}
                      </a>
                  	</h3>
                </div><!-- /.box-header -->

                <div class="box-body table-responsive no-padding">
                  <table class="table table-hover">
               <tbody>

               	@if( $data->total() !=  0 && $data->count() != 0 )
                   <tr>
                      <th class="active">ID</th>
                      <th class="active">{{ trans('misc.transaction_id') }}</th>
                      <th class="active">{{ trans('misc.amount') }}</th>
                      <th class="active">{{ trans('misc.payment_gateway') }}</th>
                      <th class="active">{{ trans('admin.date') }}</th>
                    </tr><!-- /.TR -->


                  @foreach( $data as $deposit )

                    <tr>
                      <td>{{ $deposit->id }}</td>
                      <td>{{ $deposit->txn_id }}</td>
                      <td>{{ App\Helper::amountFormat($deposit->amount) }}</td>
                      <td>{{ $deposit->payment_gateway }}</td>
                      <td>{{ date('d M, Y', strtotime($deposit->date)) }}</td>
                    </tr><!-- /.TR -->
                    @endforeach

                    @else
                    <hr />
                    	<h3 class="text-center no-found">{{ trans('misc.no_results_found') }}</h3>

                    @endif

                  </tbody>

                  </table>

                </div><!-- /.box-body -->
              </div><!-- /.box -->
              @if( $data->lastPage() > 1 )
             {{ $data->links() }}
             @endif
            </div>
          </div>

          <!-- Your Page Content Here -->

        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
@endsection
