@php
    $prefix = Request::route()->getPrefix();
    $route = Route::current()->getName();
@endphp

<div class="sidebar sidebar-fixed" id="sidebar" style="background: #0F2027;
background: -webkit-linear-gradient(to right, #133d4e, #203A43, #0F2027);
background: linear-gradient(to right, #14262e, #203A43, #0F2027);
; font-size: 15px;color:white">
    <div class="sidebar-brand d-none d-md-flex">
        <img src="@if ($dashboard_settings){{ asset('img/dashboard/'.$dashboard_settings->logo.'#full') }}
        @else {{ asset('dashboard/assets/brand/wardan_tech_logo_light.svg') }} @endif" class="sidebar-brand-full"
            width="118" height="46" alt="dashboard logo">
        <img src="@if ($dashboard_settings){{ asset('img/dashboard/'.$dashboard_settings->logo.'#signet') }}
         @else {{ asset('dashboard/assets/brand/wardan_tech_logo_light.svg') }}
          @endif" class="sidebar-brand-narrow"
            width="46" height="46" alt="dashboard logo">
    </div>
    <ul class="sidebar-nav" data-coreui="navigation" data-simplebar="">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.dashboard') }}">
                <svg class="nav-icon">
                    <use xlink:href="{{ asset('dashboard/vendors/@coreui/icons/svg/free.svg#cil-speedometer') }}"></use>
                </svg>
                {{__('sidebar.dashboard')}}
            </a>
        </li>

        {{-- Inventroy Start --}}
        <li class="nav-group">
            <a class="nav-link nav-group-toggle" href="#">
                <svg class="nav-icon">
                    <use xlink:href="{{ asset('dashboard/vendors/@coreui/icons/svg/free.svg#cil-airplay') }}"></use>
                </svg>
                {{__('sidebar.inventory')}}
            </a>
            <ul class="nav-group-items" style="height: auto;">
                <li class="nav-group" aria-expanded="false"><a class="nav-link nav-group-toggle" href="#">
                        <svg class="nav-icon">
                            <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-envelope-open"></use>
                        </svg>
                        {{__('sidebar.settings')}}
                    </a>
                    <ul class="nav-group-items" style="height: auto;">
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.inventory.settings.unit.index') }}">
                                Unit</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.inventory.settings.warehouse.index') }}">
                                Warehouse</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.inventory.settings.brand.index') }}">
                                Brand</a></li>
                    </ul>
                </li>

                <li class="nav-group" aria-expanded="false"><a class="nav-link nav-group-toggle" href="#">
                    <svg class="nav-icon">
                        <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-envelope-open"></use>
                    </svg>
                    {{__('sidebar.product')}}</a>
                    <ul class="nav-group-items" style="height: auto;">
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.inventory.products.category.index') }}">
                            Category</a>
                        </li>
                        <li class="nav-item"><a class="nav-link" href="{{ url('admin/raw-material') }}">
                            Raw-material</a>
                        </li>
                        <li class="nav-item"><a class="nav-link" href="{{ url('admin/production') }}">
                           Production</a>
                        </li>
                    </ul>
                </li>

                <li class="nav-group" aria-expanded="false"><a class="nav-link nav-group-toggle" href="#">
                    <svg class="nav-icon">
                        <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-envelope-open"></use>
                    </svg>
                    Service</a>
                    <ul class="nav-group-items" style="height: auto;">
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.inventory.services.category.index') }}">
                            Category</a>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.inventory.services.service.index') }}">
                            Service</a>
                        </li>
                    </ul>
                </li>

                <li class="nav-group" aria-expanded="false"><a class="nav-link nav-group-toggle" href="#">
                    <svg class="nav-icon">
                        <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-envelope-open"></use>
                    </svg>
                    Customer</a>
                    <ul class="nav-group-items" style="height: auto;">
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.inventory.customers.customer.index') }}">
                            Customer List</a>
                        </li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.inventory.customers.customer.index') }}">
                            Import From Client</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </li>
        {{-- Inventroy End --}}


        {{-- Project Start --}}
        <li class="nav-group">
            <a class="nav-link nav-group-toggle" href="#">
                <svg class="nav-icon">
                    <use xlink:href="{{ asset('dashboard/vendors/@coreui/icons/svg/free.svg#cil-airplay') }}"></use>
                </svg>
                {{__('sidebar.project')}}
            </a>
            <ul class="nav-group-items" style="height: auto;">
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.projects.index') }}">
                    {{__('sidebar.project')}}</a>
                </li>
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.project.category.index') }}">
                    Category</a>
                </li>
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.project.type.index') }}">
                    Type</a>
                </li>
            </ul>
        </li>
        {{-- Project End --}}

        {{-- HRM Start --}}
        <li class="nav-group">
            <a class="nav-link nav-group-toggle" href="#">
                <svg class="nav-icon">
                    <use xlink:href="{{ asset('dashboard/vendors/@coreui/icons/svg/free.svg#cil-airplay') }}"></use>
                </svg>
                {{__('sidebar.hrm')}}
            </a>
            <ul class="nav-group-items" style="height: auto;">
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.inventory.customers.customer.index') }}">
                        Salary</a>
                </li>
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.inventory.customers.customer.index') }}">
                        Attendance</a>
                </li>
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.inventory.customers.customer.index') }}">
                        Leave</a>
                </li>
                <li class="nav-group" aria-expanded="false"><a class="nav-link nav-group-toggle" href="#">
                        <svg class="nav-icon">
                            <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-envelope-open"></use>
                        </svg>
                        Settings</a>
                    <ul class="nav-group-items" style="height: auto;">
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.hrm.allowance.index') }}">
                                Allowance</a>
                        </li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.hrm.paid-leave.index') }}">
                                Paid leave</a>
                        </li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.hrm.setting.holiday.index') }}">
                                Holiday</a>
                        </li>

                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.hrm.setting.weekend.index') }}">
                            Weekend</a>
                        </li>

                    </ul>
                </li>
            </ul>
        </li>
        {{-- HRM End --}}


        <li class="nav-group">
            <a class="nav-link nav-group-toggle" href="#">
                <p class="nav-icon">
                    <i class='bx bx-money-withdraw'></i>
                </p>
                {{__('sidebar.expense')}}
            </a>
            <ul class="nav-group-items">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.expense.category.index') }}">
                        <span class="nav-icon"></span> Category
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.expense.expense.index') }}">
                            <span class="nav-icon"></span> Expense
                    </a>
                </li>
            </ul>
        </li>
        <li class="nav-group">
            <a class="nav-link nav-group-toggle" href="#">
                <p class="nav-icon">
                    <i class='bx bxs-wallet'></i>
                </p>
                {{__('sidebar.revenue')}}
            </a>
            <ul class="nav-group-items">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.revenue-category.index') }}">
                            <span class="nav-icon"></span> Category
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.revenue.index') }}">
                            <span class="nav-icon"></span> Revenue
                    </a>
                </li>
            </ul>
        </li>

        <li class="nav-group">
            <a class="nav-link nav-group-toggle" href="#">
                <svg class="nav-icon">
                    <use xlink:href="{{ asset('dashboard/vendors/@coreui/icons/svg/free.svg#cil-airplay') }}"></use>
                </svg>

                {{__('sidebar.crm')}}
            </a>
            <ul class="nav-group-items" style="height: auto;">
                <li class="nav-group" aria-expanded="false"><a class="nav-link nav-group-toggle" href="#">
                        <svg class="nav-icon">
                            <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-envelope-open"></use>
                        </svg>
                        {{__('sidebar.settings')}}</a>
                    <ul class="nav-group-items" style="height: auto;">
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.crm.interested-on.index') }}">
                                Interested On</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.crm.contact-through.index') }}">
                                Contact Through</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.crm.client-type.index') }}">
                                Client Type</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.crm.client.index') }}">
                        <span class="nav-icon"></span> Clients
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.crm.client-comment.index') }}">
                        <span class="nav-icon"></span> Comments
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.crm.client-reminder.index') }}">
                        <span class="nav-icon"></span> Reminders
                    </a>
                </li>

            </ul>
        </li>
        <li class="nav-group">
            <a class="nav-link nav-group-toggle" href="#">
                <p class="nav-icon">
                    <i class='bx bxs-bank'></i>
                    {{-- <use xlink:href="{{ asset('dashboard/vendors/@coreui/icons/svg/free.svg#cil-house') }}"></use> --}}
                </p>

                 {{__('sidebar.account')}}
            </a>
            <ul class="nav-group-items" style="height: auto;">
                <li class="nav-group">
                    <a class="nav-link nav-group-toggle" href="#">
                        <svg class="nav-icon">
                            <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-bank"></use>
                        </svg> Accounts
                    </a>
                    <ul class="nav-group-items" style="height:auto;">
                        <a class="nav-link nav-group-toggle" href="#">
                            <svg class="nav-icon">
                                <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-envelope-open"></use>
                            </svg> Setting
                        </a>
                        <ul class="nav-group-items" style="height:auto;">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.account.bank.index') }}">
                                    <span class="nav-icon"></span> Bank
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.account.bank-account.index') }}">
                                    <span class="nav-icon"></span> Account
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.account.bank.account.Statement') }}">
                                    <span class="nav-icon"></span> Account-Statement
                                </a>
                            </li>
                        </ul>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.account.balance.sheet.index') }}">
                                <span class="nav-icon"></span> Balance-Sheet
                            </a>
                        </li>
                    </ul>

                </li>
            </ul>
            <ul class="nav-group-items" style="height: auto;">
                <li class="nav-group">
                    <a class="nav-link nav-group-toggle" href="#">
                        <svg class="nav-icon">
                            <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-envelope-open"></use>
                        </svg> Cash
                    </a>
                    <ul class="nav-group-items" style="height:auto;">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.account.cash.balance.sheet.index') }}">
                                <span class="nav-icon"></span> Balance-Sheet
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.account.cash-in.index') }}">
                                <span class="nav-icon"></span>  Cash-In
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
            <ul class="nav-group-items" style="height: auto;">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.account.withdraw.index') }}">
                        <span class="nav-icon"></span> Withdraw
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.account.deposit.index') }}">
                        <span class="nav-icon"></span> Deposit
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.account.transaction.index') }}">
                        <span class="nav-icon"></span> Transaction
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.account.fund-transfer.index') }}">
                        <span class="nav-icon"></span> Fund Transfer
                    </a>
                </li>
            </ul>
        </li>
        <li class="nav-group">
            <a class="nav-link nav-group-toggle" href="#">
                <p class="nav-icon">
                    <i class='bx bx-money'></i>
                </p>
                {{__('sidebar.investment')}}
            </a>
            <ul class="nav-group-items" style="height: auto;">
                <li class="nav-group">
                    <a class="nav-link nav-group-toggle" href="#">
                        <svg class="nav-icon">
                            <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-envelope-open"></use>
                        </svg> Investment
                    </a>
                    <ul class="nav-group-items" style="height:auto;">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.investor.index') }}">
                                <span class="nav-icon"></span> Investor
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.investment.index') }}">
                                <span class="nav-icon"></span>  Investment
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
            <ul class="nav-group-items" style="height: auto;">
                <li class="nav-group">
                    <a class="nav-link nav-group-toggle" href="#">
                        <svg class="nav-icon">
                            <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-envelope-open"></use>
                        </svg> Loan
                    </a>
                    <ul class="nav-group-items" style="height:auto;">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.loan-authority.index') }}">
                                <span class="nav-icon"></span> Authority
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.loan.index') }}">
                                <span class="nav-icon"></span>  Loan
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </li>
        {{--  --}}
        <li class="nav-group">
            <a class="nav-link nav-group-toggle" href="#">
                <p class="nav-icon">
                    <i class='bx bx-group'></i>
                </p>
                {{__('sidebar.employee')}}
            </a>
            <ul class="nav-group-items" style="height: auto;">
                <li class="nav-group">
                    <a class="nav-link nav-group-toggle" href="#">
                        <svg class="nav-icon">
                            <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-envelope-open"></use>
                        </svg>Setting
                    </a>
                    <ul class="nav-group-items" style="height:auto;">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.department.index') }}">
                                Department</a>
                        </li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.designation.index') }}">
                                Designation</a></li>

                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.employee.index') }}">
                        <span class="nav-icon"></span> Employee
                    </a>
                </li>


            </ul>
        </li>
        <li class="nav-group">
            <a class="nav-link nav-group-toggle" href="#">
                <svg class="nav-icon">
                    <use
                        xlink:href="{{ asset('dashboard/vendors/@coreui/icons/svg/free.svg#cil-applications-settings') }}">
                    </use>
                </svg>
                {{__('sidebar.settings')}}
            </a>
            <ul class="nav-group-items" style="height: auto;">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.settings.dashboard.create') }}">
                        <span class="nav-icon"></span> Dashboard Setting
                    </a>
                </li>
                @can('access_role_permission')
                <li class="nav-item">
                    <a class="nav-link" href="{{route('admin.settings.role.index')}}">
                        <span class="nav-icon"></span> Role
                    </a>
                </li>
                @endcan
                <li class="nav-item">
                    <a class="nav-link" href="{{route('admin.settings.permission.index')}}">
                        <span class="nav-icon"></span> Permission
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.settings.reference.index') }}">
                        <span class="nav-icon"></span> Reference
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.settings.priority.index') }}">
                        <span class="nav-icon"></span> Priority
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.settings.identity.index') }}">
                        <span class="nav-icon"></span> Identity
                    </a>
                </li>
                <ul class="nav-group-items" style="height: auto;">
                    <li class="nav-group">
                        <a class="nav-link nav-group-toggle" href="#">
                            <svg class="nav-icon">
                                <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-envelope-open"></use>
                            </svg>Address
                        </a>
                        <ul class="nav-group-items" style="height:auto;">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.address.country.index') }}">
                                    Country</a>
                            </li>
                            <li class="nav-item"><a class="nav-link"
                                    href="{{ route('admin.address.state.index') }}">
                                    State</a>
                            </li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('admin.address.city.index') }}">
                                    City</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </ul>
        </li>

    </ul>
</div>
