@extends('layouts.header')
@section('header')
<link rel="stylesheet" href="{{asset('design/assets/libs/jvectormap/jquery-jvectormap.css')}}">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/tabler-icons/2.40.0/tabler-icons.min.css" rel="stylesheet">


@endsection
<style>
         .content-area:has(.welcome-dealer) {
            margin-top: 90px !important;
        }
        .stats-card {
            background: white;
            border: none;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            padding: 20px;
            position: relative;
            height: 130px;
        }
        
        .icon-circle {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: white;
            border: 2px solid #17a2b8;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
            box-shadow: 0 1px 4px rgba(23, 162, 184, 0.15);
        }
        
        .icon-circle i {
            font-size: 20px;
            color: #17a2b8;
        }
        
        .stats-number {
            font-size: 1.75rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 4px;
            line-height: 1.2;
        }
        
        .stats-label {
            font-size: 0.875rem;
            color: #6c757d;
            font-weight: 500;
            margin-bottom: 8px;
        }
        
        .trend-indicator {
            position: absolute;
            top: 16px;
            right: 16px;
            color: #28a745;
            font-size: 0.75rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 2px;
        }
        
        .trend-indicator i {
            font-size: 20px;
        }

        .trend-indicator.text-success {
            color: #28a745 !important;
        }
        .trend-indicator.text-danger {
            color: #dc3545 !important;
        }
        .trend-indicator.text-muted {
            color: #6c757d !important;
        }
       
        .icon-circle svg {
            width: 24px;
            height: 24px;
            stroke: #17a2b8;
        }

        @media (max-width: 576px) {
          .form-select-sm {
            font-size: 0.875rem;
          }
          
          .badge {
            font-size: 0.75rem;
            padding: 0.375rem 0.75rem;
          }
        }

        @media (max-width: 768px) {
          /* Ensure labels don't wrap awkwardly */
          .text-nowrap {
            white-space: nowrap;
          }
          
          /* Make selects take available space */
          .flex-grow-1 {
            flex: 1;
            min-width: 0;
          }
        }

        .customer-link{
          font-size: 14px !important;
        }
        
        
    </style>
@section('content')

<!--  Header End -->
          <!-- Welcome Section Start -->
          @include('alert')
          <section class="welcome">
           <div class="row">
            <div class="col-lg-12 col-xl-12">
                <div class="row custom-width-card">
                    <div class="col-sm-3 d-flex align-items-stretch">
                        <div class="card stats-card w-100 border-0">
                            <div class="icon-circle">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-currency-peso">
                                  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                  <path d="M8 19v-14h3.5a4.5 4.5 0 1 1 0 9h-3.5" />
                                  <path d="M18 8h-12" />
                                  <path d="M18 11h-12" />
                                </svg>
                            </div>
                            <div class="stats-number">
                                ₱{{ number_format($total_sales, 2) }}
                            </div>
                            <div class="stats-label">Total Sales</div>
                            <div class="trend-indicator {{ $sales_trend['trend'] == 'up' ? 'text-success' : ($sales_trend['trend'] == 'down' ? 'text-danger' : 'text-muted') }}">
                                {{ $sales_trend['percentage'] }}% 
                                <i class="ti {{ $sales_trend['icon'] }}"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-3 d-flex align-items-stretch">
                        <div class="card stats-card w-100 border-0">
                            <div class="icon-circle">
                                <i class="ti ti-shopping-cart"></i>
                            </div>
                            <div class="stats-number">
                                {{number_format($transactions_details->sum('qty'),0)}} 
                            </div>
                            <div class="stats-label">Products Sold</div>
                            <div class="trend-indicator {{ $qty_trend['trend'] == 'up' ? 'text-success' : ($qty_trend['trend'] == 'down' ? 'text-danger' : 'text-muted') }}">
                                {{ $qty_trend['percentage'] }}% 
                                <i class="ti {{ $qty_trend['icon'] }}"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-3 d-flex align-items-stretch">
                        <div class="card stats-card w-100 border-0">
                            <div class="icon-circle">
                                <i class="ti ti-map-pin"></i>
                            </div>
                            <div class="stats-number">
                                {{count($dealers)}} 
                            </div>
                            <div class="stats-label">Dealer</div>
                        </div>
                    </div>

                    <div class="col-sm-3 d-flex align-items-stretch">
                        <div class="card stats-card w-100 border-0">
                            <div class="icon-circle">
                                <i class="ti ti-users"></i>
                            </div>
                            <div class="stats-number">
                                {{$customers->count()}} 
                            </div>
                            <div class="stats-label">Customers</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
          </section>
          <section>
            <div class="row">
              <!-- Chart Section - Made smaller -->
              
                <div class="col-lg-8 col-xl-8 d-flex align-items-stretch">
                  <div class="card w-100">
                    <div class="card-body">
                      <div class="d-sm-flex justify-content-between align-items-start mb-3">
                        <h5 class="mb-3 mb-sm-0">Refill Sold Quantity</h5>
                        
                        <!-- Responsive Controls Container -->
                        <div class="d-flex flex-column flex-sm-row align-items-center justify-content-center gap-2 gap-sm-3">
                          <!-- Year Select -->
                          <div class="d-flex align-items-center">
                            <label for="yearSelect" class="me-2 mb-0 text-nowrap">Year:</label>
                            <select id="yearSelect" class="form-select form-select-sm" style="min-width: 110px;">
                              @foreach($available_years as $year)
                                <option value="{{ $year }}" {{ $year == $selected_year ? 'selected' : '' }}>
                                  {{ $year }}
                                </option>
                              @endforeach
                            </select>
                          </div>
                          
                          <!-- Month Select -->
                          <div class="d-flex align-items-center">
                            <label for="monthSelect" class="me-2 mb-0 text-nowrap">Month:</label>
                            <select id="monthSelect" class="form-select form-select-sm" style="min-width: 150px;">
                              <option value="">All Months</option>
                              @foreach($available_months as $month)
                                <option value="{{ $month['number'] }}" {{ $month['number'] == $selected_month ? 'selected' : '' }}>
                                  {{ $month['name'] }}
                                </option>
                              @endforeach
                            </select>
                          </div>
                          
                          <!-- View Mode Indicator -->
                          <span id="viewModeIndicator" class="badge bg-primary">
                            {{ $view_type === 'monthly' ? 'Daily View' : 'Monthly View' }}
                          </span>
                        </div>
                      </div>
                      
                      <!-- Loading indicator -->
                      <div id="chartLoading" class="text-center" style="display: none; padding: 50px;">
                        <div class="spinner-border text-primary" role="status">
                          <span class="visually-hidden">Loading...</span>
                        </div>
                      </div>
                      
                      <div id="chart-bar-stacked"></div>
                    </div>
                  </div>
                </div>

              <div class="col-lg-4 col-xl-4 d-flex align-items-stretch">
                <div class="card w-100">
                  <div class="card-body">
                    <div class="d-flex mb-3 justify-content-center align-items-center position-relative">
                      <div id="dealers-donut-chart"></div>
                      <div class="position-absolute" style="top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center;">
                        <small class="text-muted d-block" style="font-size: 11px;">Top 10 Dealers</small>
                        <h4 class="mb-0 fw-bold" style="font-size: 24px;">
                            {{ $dealers->isNotEmpty() ? number_format($dealers->first()->total_points) : '0' }}
                        </h4>
                      </div>
                    </div>
                    
                    <div style="max-height: 240px; overflow-y: auto; border: 1px solid #e5e7eb; border-radius: 6px;">
                      <table class="table table-bordered align-middle text-nowrap mb-0">
                        <thead class="bg-white">
                          <tr style="font-size: 11px; border-bottom: 1px solid #e5e7eb;">
                            <th scope="col" style="padding: 6px 8px; border-right: 1px solid #e5e7eb;">Dealer</th>
                            <th scope="col" style="padding: 6px 8px; border-right: 1px solid #e5e7eb;">Total Points</th>
                            <th scope="col" style="padding: 6px 8px;">Last Transaction</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach($dealers as $index => $dealer)
                            <tr style="font-size: 10px; border-bottom: 1px solid #e5e7eb;">
                              <td style="padding: 4px 8px; border-right: 1px solid #e5e7eb;">
                                <span class="d-inline-block me-1" style="width: 8px; height: 8px; border-radius: 50%; background-color: {{ ['#02437B', '#0E5A8A', '#1A7199', '#2688A8', '#329FB7', '#3EB6C6', '#4ACDD5', '#56E4E4', '#62FBF3', '#6EFFFF'][$index % 10] }};"></span>
                                {{strtoupper(substr($dealer->dealer->name ?? 'Unknown', 0, 12))}}
                              </td>
                              <td style="padding: 4px 8px; border-right: 1px solid #e5e7eb;">{{number_format($dealer->total_points,0)}}</td>
                              <td style="padding: 4px 8px;">{{date('M j, Y',strtotime($dealer->latest_transaction))}}</td>
                            </tr>
                          @endforeach
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </section>
          {{-- <div class="col-lg-12 col-xl-6 d-flex align-items-stretch">
                  <div class="card w-100">
                   
                    <div class="card-body">
                      <h5>Stove Distributed(2025)</h5>
                      <div id="chart-bar-stacked-stove"></div>
                      <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="fs-4 mb-0 fw-bold">Stove Goals</h5>
                        <p class="text-primary fw-normal fs-3 mb-0">100</p>
                      </div>
                      <div class="progress bg-light-subtle" style="height: 10px">
                        <div class="progress-bar bg-primary  rounded" style="width: 35%;height: 10px;" role="progressbar" aria-valuenow="35" aria-valuemin="0" aria-valuemax="100">35%</div>
                      </div>
                     
                    </div>
                  </div>
                </div> --}}
            <section>
              <div class="row">
                <div class="col-lg-7 col-xl-8 d-flex align-items-stretch">
                  <div class="card w-100">
                    <div class="card-body">
                      <div class="d-flex mb-4 justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">Latest Transaction</h5>
                      </div>

                      <!-- Column Headers -->
                      <div class="row mb-3 px-3" style="border-bottom: 2px solid #e2e8f0; padding-bottom: 12px;">
                        <div class="col-4">
                          <small class="text-muted fw-bold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">
                            CUSTOMER
                          </small>
                        </div>
                        <div class="col-4 text-center">
                          <small class="text-muted fw-bold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">
                            DATE
                          </small>
                        </div>
                        <div class="col-4 text-end">
                          <small class="text-muted fw-bold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">
                            CUSTOMER POINTS
                          </small>
                        </div>
                      </div>

                      <!-- Transaction List -->
                      <div class="transaction-list" style="max-height: 500px;">
                        @foreach($transactions_details as $index => $transaction)
                          <div class="transaction-item {{ $index >= 5 ? 'd-none' : '' }}" data-customer-id="{{$transaction->customer->id ?? 0}}">
                            <div class="row align-items-center p-3 mb-2 rounded-3 {{ $index % 2 == 0 ? '' : 'bg-light' }}" 
                                style="border: 1px solid rgba(229, 231, 235, 0.6);">
                              
                              <!-- Customer Column -->
                              <div class="col-4">
                                <div class="d-flex align-items-center">
                                  <!-- Customer Avatar -->
                                  <div class="flex-shrink-0 me-3">
                                    <div class="avatar-circle position-relative" style="width: 45px; height: 45px;">
                                      <img src="{{ optional($transaction->customer)->avatar ? asset($transaction->customer->avatar) : asset('design/assets/images/profile/user-1.png') }}" 
                                          alt="{{ optional($transaction->customer)->name ?? 'Customer' }}"
                                          class="rounded-circle w-100 h-100 object-fit-cover"
                                          style="border: 2px solid #e2e8f0;">
                                    </div>
                                  </div>
                                  
                                  <!-- Customer Info -->
                                  <div class="flex-grow-1">
                                    <h6 class="mb-0 fw-bold text-dark text-truncate">
                                        <a href="#" 
                                            class="text-decoration-none text-dark customer-link text-truncate d-inline-block" 
                                            style="max-width: 100%;"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#transactionModal" 
                                            onclick="showTransactionDetails('{{date('M d, Y',strtotime($transaction->created_at))}}', '{{number_format($transaction->qty,2)}}', '{{number_format($transaction->qty*$transaction->price,2)}}', '{{strtoupper($transaction->dealer->name ?? '')}}', '{{strtoupper($transaction->customer->name ?? '')}}', '{{$transaction->points_dealer}}', '{{$transaction->points_client}}', '{{$transaction->item}}')">
                                            {{ strtoupper($transaction->customer->name ?? 'Unknown') }}
                                        </a>
                                    </h6>
                                </div>
                                </div>
                              </div>
                              
                              <!-- Date Column -->
                              <div class="col-4 text-center">
                                <span class="text-dark fw-medium">
                                  {{ date('d.m.Y', strtotime($transaction->created_at)) }}
                                </span>
                              </div>
                              
                              <!-- Points Column -->
                              <div class="col-3 text-end">
                                <span class="fw-bold text-dark">
                                  {{ $transaction->points_client }}
                                </span>
                              </div>
                            </div>
                          </div>
                        @endforeach
                      </div>

                      <div class="d-flex justify-content-between align-items-center mt-3">
                        <small class="text-muted" id="entriesInfo" style="font-size: 12px;">
                          Showing <span id="currentStart">1</span> to <span id="currentEnd">5</span> of <span id="totalEntries">{{ $transactions_details->count() }}</span> entries
                        </small>
                        
                        <!-- Pagination -->
                        <nav aria-label="Transaction pagination">
                          <ul class="pagination pagination-sm mb-0">
                            <li class="page-item" id="prevPage">
                              <a class="page-link" href="javascript:void(0)" onclick="changePage('prev')" style="font-size: 12px;">
                                <i class="fas fa-chevron-left"></i> Previous
                              </a>
                            </li>
                            <li class="page-item" id="nextPage">
                              <a class="page-link" href="javascript:void(0)" onclick="changePage('next')" style="font-size: 12px;">
                                Next <i class="fas fa-chevron-right"></i>
                              </a>
                            </li>
                          </ul>
                        </nav>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="modal fade" id="transactionModal" tabindex="-1" aria-labelledby="transactionModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="transactionModalLabel">Transaction Details <span id="customerName" style="display:none"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        <div class="table-responsive">
                          <table class="table table-striped align-middle text-nowrap">
                            <thead>
                              <tr>
                                <th scope="col">Date</th>
                                <th scope="col">Quantity</th>
                                <th scope="col">Amount</th>
                                <th scope="col">Dealer</th>
                                <th scope="col">Customer</th>
                                <th scope="col">Dealer Points</th>
                                <th scope="col">Customer Points</th>
                                <th scope="col">Item</th>
                              </tr>
                            </thead>
                            <tbody id="customerTransactions">
                              <!-- Customer transactions will be here -->
                            </tbody>
                          </table>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-lg-4 col-xl-4 d-flex align-items-stretch">
                  <div class="card w-100">
                    <div class="card-body">
                      <div class="d-flex mb-3 justify-content-center align-items-center position-relative">
                        <div id="customers-donut-chart"></div>
                        <div class="position-absolute" style="top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center;">
                          <small class="text-muted d-block" style="font-size: 11px;">Top 10 Customers</small>
                          <h4 class="mb-0 fw-bold" style="font-size: 24px;">
                              {{ $top_customers->isNotEmpty() ? number_format($top_customers->first()->total_points) : '0' }}
                          </h4>
                        </div>
                      </div>
                      
                      <div style="max-height: 240px; overflow-y: auto; border: 1px solid #e5e7eb; border-radius: 6px;">
                        <table class="table table-bordered align-middle text-nowrap mb-0">
                          <thead class="bg-white">
                            <tr style="font-size: 11px; border-bottom: 1px solid #e5e7eb;">
                              <th scope="col" style="padding: 6px 8px; border-right: 1px solid #e5e7eb;">Customer</th>
                              <th scope="col" style="padding: 6px 8px; border-right: 1px solid #e5e7eb;">Total Points</th>
                              <th scope="col" style="padding: 6px 8px;">Last Transaction</th>
                            </tr>
                          </thead>
                          <tbody>
                            @foreach($top_customers as $index => $customer)
                              <tr style="font-size: 10px; border-bottom: 1px solid #e5e7eb;">
                                <td style="padding: 4px 8px; border-right: 1px solid #e5e7eb;">
                                  <span class="d-inline-block me-1" style="width: 8px; height: 8px; border-radius: 50%; background-color: {{ ['#FF6B6B', '#4ECDC4', '#45B7D1', '#FFA07A', '#98D8C8', '#F7DC6F', '#BB8FCE', '#85C1E9', '#F8C471', '#82E0AA'][$index % 10] }};"></span>
                                  {{strtoupper(substr($customer->customer->name ?? 'Unknown', 0, 12))}}
                                </td>
                                <td style="padding: 4px 8px; border-right: 1px solid #e5e7eb;">{{number_format($customer->total_points,0)}}</td>
                                <td style="padding: 4px 8px;">{{date('M j, Y',strtotime($customer->latest_transaction))}}</td>
                              </tr>
                            @endforeach
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>

              </div>
            </section>        
@endsection
@section('javascript')

<script src="https://cdn.jsdelivr.net/gh/davidshimjs/qrcodejs/qrcode.min.js"></script>
 @if(auth()->user()->role == "Client")
      <script>
          const qrcode = new QRCode(document.getElementById('qrcode'), {
              text: "{{ $customer->serial->serial_number }}",
              width: 128,
              height: 128,
              colorDark : '#000',
              colorLight : '#fff',
              correctLevel : QRCode.CorrectLevel.H
          });
      </script>
  @endif


<script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>

  {{-- <script src="../assets/js/dashboards/dashboard.js"></script> --}}


<script src="{{asset('design/assets/l ibs/jvectormap/jquery-jvectormap.min.js')}}"></script>
<script src="{{asset('design/assets/libs/apexcharts/dist/apexcharts.min.js')}}"></script>
<script src="{{asset('design/assets/js/extra-libs/jvectormap/jquery-jvectormap-us-aea-en.js')}}"></script>
<script src="{{asset('design/assets/js/dashboards/dashboard.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
{{-- <script src="{{asset('design/assets/js/dashboards/dashboard2.js')}}"></script> --}}

<script>
const allTransactions = {!! json_encode(
    $transactions_details->map(function($transaction) {
        return [
            'date' => date('M d, Y', strtotime($transaction->created_at)),
            'quantity' => number_format($transaction->qty, 2),
            'amount' => number_format($transaction->qty * $transaction->price, 2),
            'dealer' => strtoupper($transaction->dealer->name ?? ''),
            'customer' => strtoupper($transaction->customer->name ?? ''),
            'dealer_points' => $transaction->points_dealer,
            'customer_points' => $transaction->points_client,
            'item' => $transaction->item,
            'customer_id' => optional($transaction->customer)->id ?? 0
        ];
    })
) !!};

function showTransactionDetails(date, quantity, amount, dealer, customer, dealerPoints, customerPoints, item) {
    document.getElementById('customerName').textContent = customer;
    
    const tbody = document.getElementById('customerTransactions');
    tbody.innerHTML = '';
    
    const row = `
        <tr>
            <td>${date}</td>
            <td>${quantity}</td>
            <td>${amount}</td>
            <td>${dealer}</td>
            <td>${customer}</td>
            <td><span class='text-success'>${dealerPoints}</span></td>
            <td><span class='text-success'>${customerPoints}</span></td>
            <td>${item}</td>
        </tr>
    `;
    tbody.innerHTML = row;
}

function loadCustomerTransactions(customerId, customerName) {
    document.getElementById('customerName').textContent = customerName;
    
    const customerTransactions = allTransactions.filter(transaction => 
        transaction.customer_id == customerId
    );
    
    const tbody = document.getElementById('customerTransactions');
    tbody.innerHTML = '';
    
    customerTransactions.forEach(transaction => {
        const row = `
            <tr>
                <td>${transaction.date}</td>
                <td>${transaction.quantity}</td>
                <td>${transaction.amount}</td>
                <td>${transaction.dealer}</td>
                <td>${transaction.customer}</td>
                <td><span class='text-success'>${transaction.dealer_points}</span></td>
                <td><span class='text-success'>${transaction.customer_points}</span></td>
                <td>${transaction.item}</td>
            </tr>
        `;
        tbody.innerHTML += row;
    });
    
    if (customerTransactions.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center">No transactions found for this customer.</td></tr>';
    }
}

function updateTableEntries() {
    const entriesPerPage = parseInt(document.getElementById('entriesPerPage').value);
    const allRows = document.querySelectorAll('.transaction-row');
    const totalEntries = allRows.length;
    
    allRows.forEach((row, index) => {
        if (index < entriesPerPage) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
    
    const showing = Math.min(entriesPerPage, totalEntries);
    document.getElementById('entriesInfo').textContent = 
        `Showing 1 to ${showing} of ${totalEntries} entries`;
}
</script>

<script>
// Donut Chart for Top 10 Dealers
$(document).ready(function() {
  const dealersData = @json($dealers);
  const dealerNames = dealersData.slice(0, 10).map(dealer => dealer.dealer?.name || 'Unknown');
  const dealerPoints = dealersData.slice(0, 10).map(dealer => parseFloat(dealer.total_points));
  
  var donutOptions = {
    series: dealerPoints,
    chart: {
      type: 'donut',
      height: 220,
      width: 220,
      fontFamily: 'inherit',
    },
    colors: [
      '#02437B', '#0E5A8A', '#1A7199', '#2688A8', '#329FB7',
      '#3EB6C6', '#4ACDD5', '#56E4E4', '#62FBF3', '#6EFFFF'
    ],
    plotOptions: {
      pie: {
        donut: {
          size: '65%',
          labels: {
            show: false,
          }
        }
      }
    },
    dataLabels: {
      enabled: false,
    },
    legend: {
      show: false,
    },
    stroke: {
      width: 0,
    },
    tooltip: {
      enabled: true,
      y: {
        formatter: function (val) {
          return val + ' points';
        },
      },
    },
    labels: dealerNames,
  };

  var donutChart = new ApexCharts(document.querySelector("#dealers-donut-chart"), donutOptions);
  donutChart.render();
});
</script>

<script>
// Donut Chart for Top 10 Customers
$(document).ready(function() {
  const customersData = @json($top_customers ?? []);
  const customerNames = customersData.slice(0, 10).map(customer => customer.customer?.name || 'Unknown');
  const customerPoints = customersData.slice(0, 10).map(customer => parseFloat(customer.total_points));
  
  var customersDonutOptions = {
    series: customerPoints,
    chart: {
      type: 'donut',
      height: 220,
      width: 220,
      fontFamily: 'inherit',
    },
    colors: [
      '#FF6B6B', '#4ECDC4', '#45B7D1', '#FFA07A', '#98D8C8',
      '#F7DC6F', '#BB8FCE', '#85C1E9', '#F8C471', '#82E0AA'
    ],
    plotOptions: {
      pie: {
        donut: {
          size: '65%',
          labels: {
            show: false,
          }
        }
      }
    },
    dataLabels: {
      enabled: false,
    },
    legend: {
      show: false,
    },
    stroke: {
      width: 0,
    },
    tooltip: {
      enabled: true,
      y: {
        formatter: function (val) {
          return val + ' points';
        },
      },
    },
    labels: customerNames,
  };

  var customersDonutChart = new ApexCharts(document.querySelector("#customers-donut-chart"), customersDonutOptions);
  customersDonutChart.render();
});
</script>

<script>
let chartInstance = null;
const initialCategories = @json($categories);
const initialQty = @json($qty);
const initialViewType = @json($view_type);

// Initialize chart on page load
$(function () {
  renderChart(initialCategories, initialQty, {{ $selected_year }}, {{ $selected_month ?? 'null' }}, initialViewType);
  
  // Handle year selection change
  $('#yearSelect').on('change', function() {
    const selectedYear = $(this).val();
    const selectedMonth = $('#monthSelect').val();
    loadChartData(selectedYear, selectedMonth);
  });
  
  // Handle month selection change
  $('#monthSelect').on('change', function() {
    const selectedYear = $('#yearSelect').val();
    const selectedMonth = $(this).val();
    loadChartData(selectedYear, selectedMonth);
  });
});

function loadChartData(year, month = null) {
  // Disable dropdowns to prevent multiple requests
  $('#yearSelect, #monthSelect').prop('disabled', true);
  
  // Show loading indicator
  $('#chartLoading').show();
  $('#chart-bar-stacked').hide();
  
  // Add a small delay to ensure UI updates are visible
  setTimeout(() => {
    $.ajax({
      url: '{{ route("home.chart-data") }}',
      method: 'GET',
      data: { 
        year: year, 
        month: month || null 
      },
      cache: false,
      success: function(response) {
        console.log('Data loaded for year:', year, 'month:', month, response);
        
        // Update available months dropdown
        updateMonthsDropdown(response.available_months, month);
        
        // Update view mode indicator
        updateViewModeIndicator(response.view_type);
        
        // Hide loading and show chart
        $('#chartLoading').hide();
        $('#chart-bar-stacked').show();
        
        // Re-enable dropdowns
        $('#yearSelect, #monthSelect').prop('disabled', false);
        
        // Render chart with new data
        renderChart(response.categories, response.qty, response.year, response.month, response.view_type);
      },
      error: function(xhr, status, error) {
        $('#chartLoading').hide();
        $('#chart-bar-stacked').show();
        $('#yearSelect, #monthSelect').prop('disabled', false);
        console.error('Error loading chart data:', error);
        alert('Error loading data. Please try again.');
      }
    });
  }, 100);
}

function updateMonthsDropdown(availableMonths, selectedMonth) {
  const monthSelect = $('#monthSelect');
  
  // Clear existing options except "All Months"
  monthSelect.find('option:not([value=""])').remove();
  
  // Add available months
  availableMonths.forEach(function(month) {
    const isSelected = month.number == selectedMonth ? 'selected' : '';
    monthSelect.append(`<option value="${month.number}" ${isSelected}>${month.name}</option>`);
  });
}

function updateViewModeIndicator(viewType) {
  const indicator = $('#viewModeIndicator');
  if (viewType === 'monthly') {
    indicator.text('Daily View').removeClass('bg-primary').addClass('bg-success');
  } else {
    indicator.text('Monthly View').removeClass('bg-success').addClass('bg-primary');
  }
}

function renderChart(categories, qty, year, month = null, viewType = 'yearly') {
  // Destroy existing chart if it exists
  if (chartInstance) {
    chartInstance.destroy();
    chartInstance = null;
  }
  
  // Clear the chart container
  document.querySelector("#chart-bar-stacked").innerHTML = '';
  
  // Determine chart title and axis labels based on view type
  const chartTitle = viewType === 'monthly' 
    ? `Daily LPG Refills - ${getMonthName(month)} ${year}`
    : `Monthly LPG Refills - ${year}`;
    
  const xAxisTitle = viewType === 'monthly' ? 'Days' : 'Months';
  
  var options_area = {
    series: [
      {
        name: "LPG Cylinder Refills",
        data: qty,
      }
    ],
    chart: {
      fontFamily: "inherit",
      type: "area",
      height: 420,
      toolbar: {
        show: false,
      },
      background: 'transparent',
      animations: {
        enabled: true,
        easing: 'easeinout',
        speed: 800,
        animateGradually: {
          enabled: true,
          delay: 150
        },
        dynamicAnimation: {
          enabled: true,
          speed: 350
        }
      }
    },
    title: {
      text: chartTitle,
      align: 'center',
      style: {
        fontSize: '0px',
        fontWeight: 0,
        color: '#ffffffff'
      }
    },
    grid: {
      show: true,
      borderColor: "#E5E7EB",
      strokeDashArray: 0,
      position: 'back',
      xaxis: {
        lines: {
          show: false
        }
      },
      yaxis: {
        lines: {
          show: true
        }
      },
      padding: {
        top: 20,
        right: 20,
        bottom: 10,
        left: 10
      },
    },
    colors: ["#5BC2E7"],
    fill: {
      type: "gradient",
      gradient: {
        shade: "light",
        type: "vertical",
        shadeIntensity: 0.3,
        gradientToColors: ["#02437B"],
        inverseColors: false,
        opacityFrom: 1,
        opacityTo: 0.8,
        stops: [0, 100],
      },
    },
    stroke: {
      curve: "straight",
      width: 0,
    },
    plotOptions: {
      area: {
        fillTo: "origin",
      },
    },
    dataLabels: {
      enabled: false,
    },
    xaxis: {
      categories: categories,
      title: {
        text: xAxisTitle,
        offsetY: 15,
        style: {
          fontSize: '12px',
          fontWeight: 600,
          color: '#666'
        }
      },
      labels: {
        style: {
          colors: "#9CA3AF",
          fontSize: "11px",
          fontWeight: 400,
        },
        offsetY: 0,
        rotate: viewType === 'monthly' && categories.length > 15 ? -45 : 0,
        hideOverlappingLabels: true,
        maxHeight: 30,
        formatter: function (val) {
          if (viewType === 'monthly') {
            return val; // Show day numbers as is
          } else {
            // For yearly view, show abbreviated month names
            if (val && typeof val === 'string') {
              const date = new Date(val);
              if (!isNaN(date.getTime())) {
                return date.toLocaleDateString('en-US', { month: 'short' });
              }
              return val.length > 3 ? val.substring(0, 3) : val;
            }
            return val;
          }
        },
      },
      axisBorder: {
        show: false,
      },
      axisTicks: {
        show: false,
      },
      tickPlacement: 'on',
    },
    yaxis: {
      show: true,
      title: {
        text: 'Quantity',
        style: {
          fontSize: '12px',
          fontWeight: 600,
          color: '#666'
        }
      },
      labels: {
        show: true,
        style: {
          colors: "#9CA3AF",
          fontSize: "11px",
          fontWeight: 400,
        },
        formatter: function (val) {
          return Math.round(val);
        },
      },
      axisBorder: {
        show: false,
      },
      axisTicks: {
        show: false,
      },
    },
    tooltip: {
      theme: "light",
      x: {
        formatter: function(val, opts) {
          if (viewType === 'monthly') {
            return `Day ${val}`;
          } else {
            return val;
          }
        }
      },
      y: {
        formatter: function (val) {
          return val + " units";
        },
      },
    },
    legend: {
      show: false,
    },
    markers: {
      size: 0,
    },
    responsive: [
      {
        breakpoint: 768,
        options: {
          chart: {
            height: 300,
          },
          title: {
            style: {
              fontSize: '14px'
            }
          },
          xaxis: {
            labels: {
              fontSize: "10px",
              rotate: -45,
            },
            title: {
              style: {
                fontSize: '10px'
              }
            }
          },
          yaxis: {
            labels: {
              fontSize: "10px",
            },
            title: {
              style: {
                fontSize: '10px'
              }
            }
          },
        },
      },
    ],
  };

  // Create new chart instance with a slight delay to ensure DOM is ready
  setTimeout(() => {
    chartInstance = new ApexCharts(
      document.querySelector("#chart-bar-stacked"),
      options_area
    );
    chartInstance.render();
  }, 50);
}

function getMonthName(monthNumber) {
  if (!monthNumber) return '';
  const months = [
    '', 'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December'
  ];
  return months[monthNumber] || '';
}
</script>
<script>
let currentPage = 1;
let entriesPerPage = 5;
let totalEntries = {{ $transactions_details->count() }};
let totalPages = Math.ceil(totalEntries / entriesPerPage);

function updateTableEntries() {
    entriesPerPage = parseInt(document.getElementById('entriesPerPage').value);
    totalPages = Math.ceil(totalEntries / entriesPerPage);
    currentPage = 1; // Reset to first page
    showPage(currentPage);
    updatePagination();
    updateEntriesInfo();
}

function showPage(page) {
    const items = document.querySelectorAll('.transaction-item');
    const startIndex = (page - 1) * entriesPerPage;
    const endIndex = startIndex + entriesPerPage;
    
    items.forEach((item, index) => {
        if (index >= startIndex && index < endIndex) {
            item.classList.remove('d-none');
        } else {
            item.classList.add('d-none');
        }
    });
}

function changePage(direction) {
    if (direction === 'next' && currentPage < totalPages) {
        currentPage++;
    } else if (direction === 'prev' && currentPage > 1) {
        currentPage--;
    }
    showPage(currentPage);
    updatePagination();
    updateEntriesInfo();
    return false; // Prevent default behavior
}

function goToPage(page) {
    currentPage = page;
    showPage(currentPage);
    updatePagination();
    updateEntriesInfo();
    return false; // Prevent default behavior
}

function updatePagination() {
    // Remove existing page numbers except prev/next
    const pageNumbers = document.querySelector('.pagination');
    const pageItems = pageNumbers.querySelectorAll('.page-item:not(#prevPage):not(#nextPage)');
    pageItems.forEach(item => item.remove());
    
    const prevPage = document.getElementById('prevPage');
    const nextPage = document.getElementById('nextPage');
    
    // Calculate which pages to show (3 pages max)
    let startPage, endPage;
    
    if (totalPages <= 3) {
        startPage = 1;
        endPage = totalPages;
    } else {
        if (currentPage <= 2) {
            startPage = 1;
            endPage = 3;
        } else if (currentPage >= totalPages - 1) {
            startPage = totalPages - 2;
            endPage = totalPages;
        } else {
            startPage = currentPage - 1;
            endPage = currentPage + 1;
        }
    }
    
    // Add page numbers
    for (let i = startPage; i <= endPage; i++) {
        const li = document.createElement('li');
        li.className = `page-item ${i === currentPage ? 'active' : ''}`;
        li.innerHTML = `<a class="page-link" href="javascript:void(0)" onclick="goToPage(${i}); return false;" style="font-size: 12px;">${i}</a>`;
        nextPage.parentNode.insertBefore(li, nextPage);
    }
    
    // Add dots if there are more pages
    if (startPage > 1) {
        const dotsLi = document.createElement('li');
        dotsLi.className = 'page-item disabled';
        dotsLi.innerHTML = '<span class="page-link" style="font-size: 12px;">...</span>';
        prevPage.nextSibling.after(dotsLi);
    }
    
    if (endPage < totalPages) {
        const dotsLi = document.createElement('li');
        dotsLi.className = 'page-item disabled';
        dotsLi.innerHTML = '<span class="page-link" style="font-size: 12px;">...</span>';
        nextPage.parentNode.insertBefore(dotsLi, nextPage);
    }
    
    // Update prev/next button states
    document.getElementById('prevPage').classList.toggle('disabled', currentPage === 1);
    document.getElementById('nextPage').classList.toggle('disabled', currentPage === totalPages);
}

function updateEntriesInfo() {
    const startEntry = (currentPage - 1) * entriesPerPage + 1;
    const endEntry = Math.min(currentPage * entriesPerPage, totalEntries);
    
    document.getElementById('currentStart').textContent = startEntry;
    document.getElementById('currentEnd').textContent = endEntry;
    document.getElementById('totalEntries').textContent = totalEntries;
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    showPage(1);
    updatePagination();
    updateEntriesInfo();
});
</script>
{{-- <script src="{{asset('design/assets/js/apex-chart/apex.bar.init.js')}}"></script> --}}
<script src="{{asset('design/assets/js/dashboards/dashboard.js')}}"></script>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    var myModal = new bootstrap.Modal(document.getElementById('homeModal'));
    myModal.show();
  });
</script>
@endsection
