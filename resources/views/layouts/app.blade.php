<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>FinaFlow - Financial Management</title>

    <!-- Custom fonts for this template-->
    <link href="{{asset('vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{asset('css/sb-admin-2.min.css')}}" rel="stylesheet">

    <style>
        /* Custom scrollbar styles for minimalist elegant look */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 0, 0, 0.3);
        }

        /* Firefox scrollbar */
        * {
            scrollbar-width: thin;
            scrollbar-color: rgba(0, 0, 0, 0.2) transparent;
        }

        /* Ensure sidebar scroll only appears when needed */
        #accordionSidebar {
            scrollbar-width: thin;
            scrollbar-color: rgba(0, 0, 0, 0.2) transparent;
        }

        #content-wrapper {
            scrollbar-width: thin;
            scrollbar-color: rgba(0, 0, 0, 0.2) transparent;
        }

        /* Collapsible sidebar functionality */
        .sidebar.collapsed .sidebar-brand-text,
        .sidebar.collapsed .sidebar-heading,
        .sidebar.collapsed .nav-link span {
            display: none !important;
        }

        .sidebar.collapsed .nav-link {
            text-align: center;
            padding: 0.75rem 0.5rem;
            position: relative;
            display: block !important;
            min-height: 50px;
        }

        .sidebar.collapsed .nav-link i {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            font-size: 1.5rem !important;
            color: rgba(255, 255, 255, 0.9) !important;
            margin: 0 auto !important;
            width: 24px;
            height: 24px;
            line-height: 24px;
            background: transparent !important;
            border: none !important;
        }

        .sidebar.collapsed {
            width: 70px !important;
        }

        .sidebar.collapsed .sidebar-brand-icon {
            margin-right: 0;
        }

        /* Enhanced visibility for icons in collapsed sidebar */
        .sidebar.collapsed .nav-link {
            position: relative;
            transition: all 0.3s ease;
            min-height: 50px;
            text-align: left;
            padding-left: 1rem;
        }

        .sidebar.collapsed .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
        }

        .sidebar.collapsed .nav-link:hover i {
            font-size: 1.4rem;
            color: rgba(255, 255, 255, 0.9);
            transform: scale(1.1);
        }

        .sidebar.collapsed .nav-link i {
            transition: all 0.3s ease;
            line-height: 1;
            float: left;
            margin-right: 0;
        }

        /* Toggle button styling */
        #sidebarToggle {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(255, 255, 255, 0.2);
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgba(255, 255, 255, 0.8);
            transition: all 0.3s ease;
        }

        #sidebarToggle:hover {
            background: rgba(255, 255, 255, 0.3);
            color: white;
        }

        .sidebar.collapsed #sidebarToggle {
            left: 50%;
        }

        .sidebar.collapsed #sidebarToggle i {
            transform: rotate(180deg);
        }
    </style>

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper" class="d-flex" style="min-height: 100vh;">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar" style="flex-shrink: 0; height: 100vh; overflow-y: auto;">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard') }}">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="sidebar-brand-text mx-3">FinaFlow</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="{{ route('dashboard') }}">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>{{ __('dashboard.title') }}</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Financial Management
            </div>

            <!-- Nav Item - Settings -->
            <li class="nav-item">
                <a class="nav-link" href="{{ route('settings.index') }}">
                    <i class="fas fa-fw fa-cogs"></i>
                    <span>{{ __('navigation.settings') }}</span></a>
            </li>

            <!-- Nav Item - Privacy Settings -->
            <li class="nav-item">
                <a class="nav-link" href="{{ route('privacy.settings') }}">
                    <i class="fas fa-fw fa-shield-alt"></i>
                    <span>{{ __('navigation.privacy_settings') }}</span></a>
            </li>

            <!-- Nav Item - AI Insights -->
              <li class="nav-item">
                  <a class="nav-link" href="{{ route('insights.index') }}">
                      <i class="fas fa-fw fa-brain"></i>
                      <span>{{ __('navigation.ai_insights') }}</span></a>
              </li>

              <!-- Nav Item - Custom Reporting -->
              <li class="nav-item">
                  <a class="nav-link" href="{{ route('reporting.dashboard') }}">
                      <i class="fas fa-fw fa-chart-area"></i>
                      <span>{{ __('navigation.custom_reporting') }}</span></a>
              </li>

              <!-- Nav Item - Financial Education -->
              <li class="nav-item">
                  <a class="nav-link" href="{{ route('education.index') }}">
                      <i class="fas fa-fw fa-graduation-cap"></i>
                      <span>{{ __('navigation.financial_education') }}</span></a>
              </li>

            <!-- Nav Item - Categories -->
            <li class="nav-item">
                <a class="nav-link" href="{{ route('categories.index') }}">
                    <i class="fas fa-fw fa-tags"></i>
                    <span>{{ __('navigation.categories') }}</span></a>
            </li>

            <!-- Nav Item - Accounts -->
            <li class="nav-item">
                <a class="nav-link" href="{{ route('accounts.index') }}">
                    <i class="fas fa-fw fa-wallet"></i>
                    <span>{{ __('navigation.accounts') }}</span></a>
            </li>

            <!-- Nav Item - Transactions -->
            <li class="nav-item">
                <a class="nav-link" href="{{ route('transactions.index') }}">
                    <i class="fas fa-fw fa-exchange-alt"></i>
                    <span>{{ __('transactions.title') }}</span></a>
            </li>

            <!-- Nav Item - Transfers -->
            <li class="nav-item">
                <a class="nav-link" href="{{ route('transfers.index') }}">
                    <i class="fas fa-fw fa-random"></i>
                    <span>{{ __('navigation.transfers') }}</span></a>
            </li>

            <!-- Nav Item - Goals -->
            <li class="nav-item">
                <a class="nav-link" href="{{ route('goals.index') }}">
                    <i class="fas fa-fw fa-bullseye"></i>
                    <span>{{ __('navigation.goals') }}</span></a>
            </li>

            <!-- Nav Item - Budgets -->
            <li class="nav-item">
                <a class="nav-link" href="{{ route('budgets.index') }}">
                    <i class="fas fa-fw fa-calculator"></i>
                    <span>{{ __('navigation.budgets') }}</span></a>
            </li>

            <!-- Nav Item - Investments -->
            <li class="nav-item">
                <a class="nav-link" href="{{ route('investments.index') }}">
                    <i class="fas fa-fw fa-chart-line"></i>
                    <span>{{ __('navigation.investments') }}</span></a>
            </li>

            <!-- Nav Item - Assets -->
            <li class="nav-item">
                <a class="nav-link" href="{{ route('assets.index') }}">
                    <i class="fas fa-fw fa-home"></i>
                    <span>{{ __('navigation.assets') }}</span></a>
            </li>

            <!-- Nav Item - Net Worth -->
            <li class="nav-item">
                <a class="nav-link" href="{{ route('net-worth.index') }}">
                    <i class="fas fa-fw fa-chart-pie"></i>
                    <span>{{ __('navigation.net_worth') }}</span></a>
            </li>

            <!-- Nav Item - Analytics -->
            <li class="nav-item">
                <a class="nav-link" href="{{ route('analytics.index') }}">
                    <i class="fas fa-fw fa-chart-area"></i>
                    <span>{{ __('navigation.analytics') }}</span></a>
            </li>

            <!-- Nav Item - Debts -->
            <li class="nav-item">
                <a class="nav-link" href="{{ route('debts.index') }}">
                    <i class="fas fa-fw fa-credit-card"></i>
                    <span>{{ __('navigation.debts') }}</span></a>
            </li>

            <!-- Nav Item - Tags -->
            <li class="nav-item">
                <a class="nav-link" href="{{ route('tags.index') }}">
                    <i class="fas fa-fw fa-hashtag"></i>
                    <span>{{ __('navigation.tags') }}</span>
                </a>
            </li>

            <!-- Nav Item - Tax Documents -->
            <li class="nav-item">
                <a class="nav-link" href="{{ route('tax-documents.index') }}">
                    <i class="fas fa-fw fa-file-invoice-dollar"></i>
                    <span>{{ __('navigation.tax_documents') }}</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Behavioral Finance
            </div>

            <!-- Nav Item - Behavioral -->
            <li class="nav-item">
                <a class="nav-link" href="{{ route('behavioral.index') }}">
                    <i class="fas fa-fw fa-brain"></i>
                    <span>{{ __('navigation.behavioral_insights') }}</span>
                </a>
            </li>

            <!-- Nav Item - Subscriptions -->
            <li class="nav-item">
                <a class="nav-link" href="{{ route('subscriptions.index') }}">
                    <i class="fas fa-fw fa-sync-alt"></i>
                    <span>{{ __('navigation.subscriptions') }}</span>
                </a>
            </li>

            <!-- Nav Item - Rewards -->
            <li class="nav-item">
                <a class="nav-link" href="{{ route('rewards.index') }}">
                    <i class="fas fa-fw fa-gift"></i>
                    <span>{{ __('navigation.rewards_loyalty') }}</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <div class="sidebar-heading">
                Automation &amp; Integrations
            </div>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('automations.index') }}">
                    <i class="fas fa-fw fa-robot"></i>
                    <span>{{ __('navigation.automations') }}</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('bank-integrations.index') }}">
                    <i class="fas fa-fw fa-university"></i>
                    <span>{{ __('navigation.bank_integrations') }}</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('api-integrations.index') }}">
                    <i class="fas fa-fw fa-plug"></i>
                    <span>{{ __('navigation.api_integrations') }}</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('integrations.voice-entry') }}">
                    <i class="fas fa-fw fa-microphone"></i>
                    <span>{{ __('navigation.integration_tools') }}</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Family Finance
            </div>

            <!-- Nav Item - Family -->
            <li class="nav-item">
                <a class="nav-link" href="{{ route('family.index') }}">
                    <i class="fas fa-fw fa-users"></i>
                    <span>{{ __('navigation.family_finance') }}</span>
                </a>
            </li>

                <!-- Divider -->
                <hr class="sidebar-divider d-none d-md-block">

                <!-- Sidebar Toggler (Sidebar) -->
                <div class="text-center d-none d-md-inline">
                    <button class="rounded-circle border-0" id="sidebarToggle" style="display: none;">
                        <i class="fas fa-angle-left"></i>
                    </button>
                </div>

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column flex-grow-1" style="overflow-y: auto; height: 100vh;">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 shadow" style="position: sticky; top: 0; z-index: 1030;">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Sidebar Toggle (Desktop) -->
                    <button id="sidebarToggleDesktop" class="btn btn-link d-none d-md-inline-block rounded-circle mr-3" title="Toggle Sidebar">
                        <i class="fas fa-bars"></i>
                    </button>

                    <!-- Topbar Search -->
                    <form
                        class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                        <div class="input-group">
                            <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..."
                                aria-label="Search" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Language Switcher -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="languageDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-globe"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="languageDropdown">
                                <a class="dropdown-item {{ app()->getLocale() == 'en' ? 'active' : '' }}" href="{{ route('language.switch', 'en') }}">
                                    <i class="fas fa-language fa-sm fa-fw mr-2 text-gray-400"></i>
                                    English
                                </a>
                                <a class="dropdown-item {{ app()->getLocale() == 'id' ? 'active' : '' }}" href="{{ route('language.switch', 'id') }}">
                                    <i class="fas fa-language fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Bahasa Indonesia
                                </a>
                            </div>
                        </li>

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{ Auth::check() ? Auth::user()->name : 'Guest' }}</span>
                                <img class="img-profile rounded-circle"
                                    src="img/undraw_profile.svg">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="{{ route('profile.show') }}">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    {{ __('navigation.profile') }}
                                </a>
                                <a class="dropdown-item" href="{{ route('privacy.settings') }}">
                                    <i class="fas fa-shield-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    {{ __('navigation.privacy_settings') }}
                                </a>
                                <a class="dropdown-item" href="{{ route('settings.index') }}">
                                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                    {{ __('navigation.settings') }}
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                                    {{ __('navigation.activity_log') }}
                                </a>
                                <div class="dropdown-divider"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                        {{ __('auth.logout') }}
                                    </button>
                                </form>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid" style="padding-bottom: 2rem;">
                    @yield('content')
                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; FinaFlow 2024</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Bootstrap core JavaScript-->
    <script src="{{asset('vendor/jquery/jquery.min.js')}}"></script>
    <script src="{{asset('vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>

    <!-- Core plugin JavaScript-->
    <script src="{{asset('vendor/jquery-easing/jquery.easing.min.js')}}"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{asset('js/sb-admin-2.min.js')}}"></script>

    <!-- Custom sidebar toggle script -->
    <script>
        $(document).ready(function() {
            // Function to toggle sidebar
            function toggleSidebar() {
                $('#accordionSidebar').toggleClass('collapsed');
                // Store the state in localStorage
                const isCollapsed = $('#accordionSidebar').hasClass('collapsed');
                localStorage.setItem('sidebarCollapsed', isCollapsed);
            }

            // Sidebar toggle button in sidebar
            $('#sidebarToggle').on('click', function() {
                toggleSidebar();
            });

            // Sidebar toggle button in topbar (desktop)
            $('#sidebarToggleDesktop').on('click', function() {
                toggleSidebar();
            });

            // Sidebar toggle button in topbar (mobile)
            $('#sidebarToggleTop').on('click', function() {
                toggleSidebar();
            });

            // Restore sidebar state on page load
            const sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            if (sidebarCollapsed) {
                $('#accordionSidebar').addClass('collapsed');
            }
        });
    </script>
