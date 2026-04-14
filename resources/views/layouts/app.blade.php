<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="FinaFlow - Scandinavian Clean UI">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>FinaFlow - Financial Management</title>

    <!-- Custom fonts for this template-->
    <link href="{{asset('vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet" type="text/css">
    
    <!-- Option 2: Clean Scandinavian Font (Inter) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{asset('css/sb-admin-2.min.css')}}" rel="stylesheet">

    <link href="{{ asset('css/finaflow-theme.css') }}" rel="stylesheet">

</head>

<body id="page-top">

    @if(Auth::check() && ! Auth::user()->is_active)
        <div class="account-locked-overlay">
            <div class="account-locked-card">
                <h4 class="mb-2 text-danger">Access Disabled</h4>
                <p class="mb-3">{{ session('account_inactive_message') ?? 'Your account has been disabled. Please contact the administrator to re-activate.' }}</p>
                <a href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form-overlay').submit();"
                   class="btn btn-primary btn-sm">Log Out</a>
                <form id="logout-form-overlay" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </div>
    @endif

    <!-- Page Wrapper -->
    <div id="wrapper" class="d-flex" style="min-height: 100vh;">

        <!-- Sidebar -->
        <ul class="navbar-nav sidebar sidebar-light accordion" id="accordionSidebar" style="flex-shrink: 0; background-color: #ffffff;">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center" href="{{ route('dashboard') }}">
                <div class="sidebar-brand-icon">
                    <img src="{{ asset('img/logo.svg') }}" alt="Logo" width="32" onerror="this.outerHTML='<i class=\'fas fa-chart-line\'></i>'">
                </div>
                <div class="sidebar-brand-text mx-3 flex-grow-1">FinaFlow</div>
                <button type="button" class="btn btn-link d-lg-none text-gray-500 p-2 ml-auto" id="sidebarCloseMobile" style="font-size: 1.75rem; text-decoration: none; line-height: 1; z-index: 1050; position: relative;" aria-label="Close">&times;</button>
            </a>

            <div class="sidebar-scroll">
                <!-- Divider -->
                <hr class="sidebar-divider my-0">

                @can('access dashboard')
                    <!-- Nav Item - Dashboard -->
                    <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('dashboard') }}">
                            <i class="fas fa-fw fa-tachometer-alt"></i>
                            <span>{{ __('dashboard.title') }}</span></a>
                    </li>
                @endcan

                <!-- Divider -->
                <hr class="sidebar-divider">

                @can('access dashboard')
                    <!-- Heading -->
                    <div class="sidebar-heading">
                        Financial Management
                    </div>
                @endcan

                <!-- Nav Item - Settings -->
                <li class="nav-item {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('settings.index') }}">
                        <i class="fas fa-fw fa-cogs"></i>
                        <span>{{ __('navigation.settings') }}</span></a>
                </li>

                <!-- Nav Item - Privacy Settings -->
                <li class="nav-item {{ request()->routeIs('privacy.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('privacy.settings') }}">
                        <i class="fas fa-fw fa-shield-alt"></i>
                        <span>{{ __('navigation.privacy_settings') }}</span></a>
                </li>

                <!-- Nav Item - AI Insights -->
                  <li class="nav-item {{ request()->routeIs('insights.*') ? 'active' : '' }}">
                      <a class="nav-link" href="{{ route('insights.index') }}">
                          <i class="fas fa-fw fa-brain"></i>
                          <span>{{ __('navigation.ai_insights') }}</span></a>
                  </li>

                  @can('view reports')
                      <!-- Nav Item - Custom Reporting -->
                      <li class="nav-item {{ request()->routeIs('reporting.*') ? 'active' : '' }}">
                          <a class="nav-link" href="{{ route('reporting.dashboard') }}">
                              <i class="fas fa-fw fa-chart-area"></i>
                              <span>{{ __('navigation.custom_reporting') }}</span></a>
                      </li>
                  @endcan

                  @can('manage behavioral')
                      <!-- Nav Item - Financial Education -->
                      <li class="nav-item {{ request()->routeIs('education.*') ? 'active' : '' }}">
                          <a class="nav-link" href="{{ route('education.index') }}">
                              <i class="fas fa-fw fa-graduation-cap"></i>
                              <span>{{ __('navigation.financial_education') }}</span></a>
                      </li>
                      <li class="nav-item {{ request()->routeIs('coaching.*') ? 'active' : '' }}">
                          <a class="nav-link" href="{{ route('coaching.index') }}">
                              <i class="fas fa-fw fa-hands-helping"></i>
                              <span>{{ __('navigation.financial_coaching') }}</span></a>
                      </li>
                  @endcan

                  <!-- Nav Item - Categories -->
                  <li class="nav-item {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                      <a class="nav-link" href="{{ route('categories.index') }}">
                          <i class="fas fa-fw fa-tags"></i>
                          <span>{{ __('navigation.categories') }}</span></a>
                  </li>

                  @can('manage accounts')
                      <!-- Nav Item - Accounts -->
                      <li class="nav-item {{ request()->routeIs('accounts.*') ? 'active' : '' }}">
                          <a class="nav-link" href="{{ route('accounts.index') }}">
                              <i class="fas fa-fw fa-wallet"></i>
                              <span>{{ __('navigation.accounts') }}</span></a>
                      </li>
                  @endcan

                  @can('manage transactions')
                      <!-- Nav Item - Transactions -->
                      <li class="nav-item {{ request()->routeIs('transactions.*') ? 'active' : '' }}">
                          <a class="nav-link" href="{{ route('transactions.index') }}">
                              <i class="fas fa-fw fa-exchange-alt"></i>
                              <span>{{ __('transactions.title') }}</span></a>
                      </li>
                  @endcan

                  @canany(['manage accounts', 'manage transactions'])
                      <!-- Nav Item - Transfers -->
                      <li class="nav-item {{ request()->routeIs('transfers.*') ? 'active' : '' }}">
                          <a class="nav-link" href="{{ route('transfers.index') }}">
                              <i class="fas fa-fw fa-random"></i>
                              <span>{{ __('navigation.transfers') }}</span></a>
                      </li>
                  @endcanany

                  @can('manage goals')
                      <!-- Nav Item - Goals -->
                      <li class="nav-item {{ request()->routeIs('goals.*') ? 'active' : '' }}">
                          <a class="nav-link" href="{{ route('goals.index') }}">
                              <i class="fas fa-fw fa-bullseye"></i>
                              <span>{{ __('navigation.goals') }}</span></a>
                      </li>
                  @endcan

                  @can('manage budgets')
                      <!-- Nav Item - Budgets -->
                      <li class="nav-item {{ request()->routeIs('budgets.*') ? 'active' : '' }}">
                          <a class="nav-link" href="{{ route('budgets.index') }}">
                              <i class="fas fa-fw fa-calculator"></i>
                              <span>{{ __('navigation.budgets') }}</span></a>
                      </li>
                  @endcan

                  @can('view reports')
                      <!-- Nav Item - Investments -->
                      <li class="nav-item {{ request()->routeIs('investments.*') ? 'active' : '' }}">
                          <a class="nav-link" href="{{ route('investments.index') }}">
                              <i class="fas fa-fw fa-chart-line"></i>
                              <span>{{ __('navigation.investments') }}</span></a>
                      </li>

                      <!-- Nav Item - Assets -->
                      <li class="nav-item {{ request()->routeIs('assets.*') ? 'active' : '' }}">
                          <a class="nav-link" href="{{ route('assets.index') }}">
                              <i class="fas fa-fw fa-home"></i>
                              <span>{{ __('navigation.assets') }}</span></a>
                      </li>

                      <!-- Nav Item - Net Worth -->
                      <li class="nav-item {{ request()->routeIs('net-worth.*') ? 'active' : '' }}">
                          <a class="nav-link" href="{{ route('net-worth.index') }}">
                              <i class="fas fa-fw fa-chart-pie"></i>
                              <span>{{ __('navigation.net_worth') }}</span></a>
                      </li>

                      <!-- Nav Item - Analytics -->
                      <li class="nav-item {{ request()->routeIs('analytics.*') ? 'active' : '' }}">
                          <a class="nav-link" href="{{ route('analytics.index') }}">
                              <i class="fas fa-fw fa-chart-area"></i>
                              <span>{{ __('navigation.analytics') }}</span></a>
                      </li>
                  @endcan

                  @can('manage budgets')
                      <!-- Nav Item - Debts -->
                      <li class="nav-item {{ request()->routeIs('debts.*') ? 'active' : '' }}">
                          <a class="nav-link" href="{{ route('debts.index') }}">
                              <i class="fas fa-fw fa-credit-card"></i>
                              <span>{{ __('navigation.debts') }}</span></a>
                      </li>
                  @endcan

                <!-- Nav Item - Category Reports -->
                <li class="nav-item {{ request()->routeIs('reports.categories.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('reports.categories.index') }}">
                        <i class="fas fa-fw fa-chart-bar"></i>
                        <span>{{ __('navigation.category_reports') }}</span></a>
                </li>

                  @can('manage transactions')
                      <!-- Nav Item - Tags -->
                      <li class="nav-item {{ request()->routeIs('tags.*') ? 'active' : '' }}">
                          <a class="nav-link" href="{{ route('tags.index') }}">
                              <i class="fas fa-fw fa-hashtag"></i>
                              <span>{{ __('navigation.tags') }}</span>
                          </a>
                      </li>
                  @endcan

                  @can('view reports')
                      <!-- Nav Item - Tax Documents -->
                      <li class="nav-item {{ request()->routeIs('tax-documents.*') ? 'active' : '' }}">
                          <a class="nav-link" href="{{ route('tax-documents.index') }}">
                              <i class="fas fa-fw fa-file-invoice-dollar"></i>
                              <span>{{ __('navigation.tax_documents') }}</span>
                          </a>
                      </li>
                  @endcan
                  
@can('manage behavioral')
                <!-- Divider -->
                <hr class="sidebar-divider">

                <!-- Heading -->
                <div class="sidebar-heading">
                    Behavioral Finance
                </div>
                    <!-- Nav Item - Behavioral -->
                    <li class="nav-item {{ request()->routeIs('behavioral.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('behavioral.index') }}">
                            <i class="fas fa-fw fa-brain"></i>
                            <span>{{ __('navigation.behavioral_insights') }}</span>
                        </a>
                    </li>
                @endcan

                @can('manage family')
                    <!-- Nav Item - Subscriptions -->
                    <li class="nav-item {{ request()->routeIs('subscriptions.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('subscriptions.index') }}">
                            <i class="fas fa-fw fa-sync-alt"></i>
                            <span>{{ __('navigation.subscriptions') }}</span>
                        </a>
                    </li>

                    <!-- Nav Item - Rewards -->
                    <li class="nav-item {{ request()->routeIs('rewards.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('rewards.index') }}">
                            <i class="fas fa-fw fa-gift"></i>
                            <span>{{ __('navigation.rewards_loyalty') }}</span>
                        </a>
                    </li>
                @endcan

                <!-- Divider -->
                <hr class="sidebar-divider">

                @can('manage automations')
                    <div class="sidebar-heading">
                        Automation &amp; Integrations
                    </div>

                    <li class="nav-item {{ request()->routeIs('automations.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('automations.index') }}">
                            <i class="fas fa-fw fa-robot"></i>
                            <span>{{ __('navigation.automations') }}</span>
                        </a>
                    </li>

                    <li class="nav-item {{ request()->routeIs('bank-integrations.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('bank-integrations.index') }}">
                            <i class="fas fa-fw fa-university"></i>
                            <span>{{ __('navigation.bank_integrations') }}</span>
                        </a>
                    </li>

                    <li class="nav-item {{ request()->routeIs('api-integrations.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('api-integrations.index') }}">
                            <i class="fas fa-fw fa-plug"></i>
                            <span>{{ __('navigation.api_integrations') }}</span>
                        </a>
                    </li>

                    <li class="nav-item {{ request()->routeIs('integrations.telegram-bot') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('integrations.telegram-bot') }}">
                            <i class="fas fa-fw fa-paper-plane"></i>
                            <span>{{ __('navigation.telegram_bot') ?? 'Telegram Bot' }}</span>
                        </a>
                    </li>

                    <li class="nav-item {{ request()->routeIs('integrations.voice-entry') || request()->routeIs('integrations.email-parser') || request()->routeIs('integrations.reminders') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('integrations.voice-entry') }}">
                            <i class="fas fa-fw fa-microphone"></i>
                            <span>{{ __('navigation.integration_tools') }}</span>
                        </a>
                    </li>
                @endcan

                @can('access admin')
                <hr class="sidebar-divider">
                <div class="sidebar-heading">
                    Admin
                </div>
                @can('manage users')
                    <li class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.users.index') }}">
                            <i class="fas fa-fw fa-user-shield"></i>
                            <span>Kelola Pengguna</span>
                        </a>
                    </li>
                @endcan
                @can('manage education')
                    <li class="nav-item {{ request()->routeIs('admin.education.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.education.index') }}">
                            <i class="fas fa-fw fa-book"></i>
                            <span>Education Admin</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('admin.education-categories.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.education-categories.index') }}">
                            <i class="fas fa-fw fa-list"></i>
                            <span>Education Categories</span>
                        </a>
                    </li>
                @endcan
                @can('manage news')
                    <li class="nav-item {{ request()->routeIs('admin.news.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.news.index') }}">
                            <i class="fas fa-fw fa-newspaper"></i>
                            <span>News Admin</span>
                        </a>
                    </li>
                @endcan
                @endcan
                
                @can('manage family')
                <!-- Divider -->
                <hr class="sidebar-divider">
                    <!-- Heading -->
                    <div class="sidebar-heading">
                        Family Finance
                    </div>
                    <!-- Nav Item - Family -->
                    <li class="nav-item {{ request()->routeIs('family.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('family.index') }}">
                            <i class="fas fa-fw fa-users"></i>
                            <span>{{ __('navigation.family_finance') }}</span>
                        </a>
                    </li>
                @endcan
            </div>
        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column flex-grow-1">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light topbar mb-2 static-top px-4">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="customSidebarToggleTop" class="btn btn-link d-md-none mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Sidebar Toggle (Desktop) -->
                    <button id="sidebarToggleDesktop" class="btn btn-link d-none d-md-flex mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Search -->
                    <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search position-relative">
                        <i class="fas fa-search topbar-search-icon"></i>
                        <input type="text" class="form-control bg-light border-0 small" placeholder="Search..." aria-label="Search">
                    </form>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto align-items-center">

                        <li class="nav-item mx-1">
                            <a class="nav-link" href="#">
                                <i class="far fa-file-alt text-gray-400"></i>
                            </a>
                        </li>

                        <li class="nav-item mx-1">
                            <a class="nav-link" href="#">
                                <i class="far fa-bell text-gray-400"></i>
                            </a>
                        </li>

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small font-weight-bold">{{ Auth::check() ? Auth::user()->name : 'Guest' }}</span>
                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-light text-primary font-weight-bold" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                    {{ Auth::check() ? substr(Auth::user()->name, 0, 1) : 'G' }}
                                </div>
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in border-0" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="{{ route('profile.show') }}">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    {{ __('navigation.profile') }}
                                </a>
                                <a class="dropdown-item" href="{{ route('privacy.settings') }}">
                                    <i class="fas fa-shield-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    {{ __('navigation.privacy_settings') }}
                                </a>
                                <a class="dropdown-item" href="{{ route('twofactor.setup') }}">
                                    <i class="fas fa-lock fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Two-Factor Auth
                                </a>
                                <a class="dropdown-item" href="{{ route('settings.index') }}">
                                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                    {{ __('navigation.settings') }}
                                </a>
                                <a class="dropdown-item" href="{{ route('activity-log.index') }}">
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
                <div id="pageContent" class="container-fluid" style="padding-bottom: 2rem;">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
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

    <div id="sidebarOverlay" class="sidebar-overlay"></div>

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Core plugin JavaScript-->
    <script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('vendor/chart.js/Chart.min.js') }}"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>

    <!-- Custom sidebar toggle script -->
    <script>
        function updateSidebarActiveState(doc) {
            const sidebarLinks = document.querySelectorAll('#accordionSidebar .nav-link');
            sidebarLinks.forEach(link => link.parentElement.classList.remove('active'));

            const newActiveLink = doc.querySelector('#accordionSidebar .nav-item.active .nav-link');
            if (!newActiveLink) {
                return;
            }

            const activeHref = newActiveLink.getAttribute('href');
            sidebarLinks.forEach(link => {
                if (link.getAttribute('href') === activeHref) {
                    link.parentElement.classList.add('active');
                }
            });
        }

        function executePageScripts(container) {
            const scripts = Array.from(container.querySelectorAll('script'));

            const loadScript = function (index) {
                if (index >= scripts.length) {
                    return;
                }

                const script = scripts[index];

                if (script.src && document.querySelector(`script[src="${script.src}"]`)) {
                    loadScript(index + 1);
                    return;
                }

                const newScript = document.createElement('script');
                newScript.async = false;

                if (script.src) {
                    newScript.src = script.src;
                } else {
                    newScript.textContent = script.textContent;
                }

                [...script.attributes].forEach((attribute) => {
                    if (attribute.name === 'src' && script.src) {
                        return;
                    }

                    newScript.setAttribute(attribute.name, attribute.value);
                });

                newScript.onload = function () {
                    loadScript(index + 1);
                };

                newScript.onerror = function () {
                    loadScript(index + 1);
                };

                document.body.appendChild(newScript);

                if (!script.src) {
                    loadScript(index + 1);
                }
            };

            loadScript(0);
        }

        function loadPage(url, pushToHistory = true) {
            const contentContainer = document.getElementById('pageContent');
            const sidebarScroll = document.querySelector('.sidebar-scroll');
            const sidebarScrollTop = sidebarScroll ? sidebarScroll.scrollTop : 0;

            if (!contentContainer) {
                window.location.href = url;
                return;
            }

            contentContainer.classList.add('content-loading');

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then(response => {
                if (!response.ok) {
                    throw new Error('Failed to load content');
                }

                return response.text();
            }).then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newContent = doc.getElementById('pageContent');

                if (!newContent) {
                    window.location.href = url;
                    return;
                }

                contentContainer.innerHTML = newContent.innerHTML;
                executePageScripts(contentContainer);

                const newTitle = doc.querySelector('title');
                if (newTitle) {
                    document.title = newTitle.textContent;
                }

                updateSidebarActiveState(doc);
                if (sidebarScroll) {
                    sidebarScroll.scrollTop = sidebarScrollTop;
                    window.sessionStorage.setItem('sidebarScrollTop', String(sidebarScrollTop));
                }

                if (pushToHistory) {
                    history.pushState({ url }, '', url);
                }

                if (typeof contentContainer.scrollTo === 'function') {
                    contentContainer.scrollTo({ top: 0, behavior: 'smooth' });
                } else {
                    contentContainer.scrollTop = 0;
                }
            }).catch(() => {
                window.location.href = url;
            }).finally(() => {
                contentContainer.classList.remove('content-loading');
            });
        }

        $(document).ready(function() {
            const $sidebar = $('#accordionSidebar');
            const $sidebarOverlay = $('#sidebarOverlay');
            const $body = $('body');
            let wasMobileView = isMobileView();

            function isMobileView() {
                return window.matchMedia('(max-width: 991.98px)').matches;
            }

            function openMobileSidebar() {
                $body.addClass('sidebar-open');
                $sidebar.addClass('mobile-open');
            }

            function closeMobileSidebar() {
                $body.removeClass('sidebar-open sidebar-toggled');
                $sidebar.removeClass('mobile-open');
            }

            function toggleSidebar() {
                if (isMobileView()) {
                    if ($sidebar.hasClass('mobile-open')) {
                        closeMobileSidebar();
                    } else {
                        openMobileSidebar();
                    }

                    return;
                }

                $sidebar.toggleClass('collapsed');
                const isCollapsed = $sidebar.hasClass('collapsed');
                localStorage.setItem('sidebarCollapsed', isCollapsed);
                if (isCollapsed) {
                    $body.addClass('sidebar-toggled');
                } else {
                    $body.removeClass('sidebar-toggled');
                }
            }

            function applyStoredSidebarState() {
                const storedCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
                if (storedCollapsed) {
                    $sidebar.addClass('collapsed');
                    $body.addClass('sidebar-toggled');
                } else {
                    $sidebar.removeClass('collapsed');
                    $body.removeClass('sidebar-toggled');
                }
            }

            function handleResponsiveSidebar() {
                const mobileView = isMobileView();

                if (mobileView && !wasMobileView) {
                    $sidebar.removeClass('collapsed');
                    closeMobileSidebar();
                } else if (!mobileView && wasMobileView) {
                    closeMobileSidebar();
                    applyStoredSidebarState();
                } else if (!mobileView) {
                    applyStoredSidebarState();
                }

                wasMobileView = mobileView;
            }

            // Sidebar toggle button in sidebar
            $('#customSidebarToggle').on('click', function() {
                toggleSidebar();
            });

            // Sidebar toggle button in topbar (desktop)
            $('#sidebarToggleDesktop').on('click', function() {
                toggleSidebar();
            });

            // Sidebar toggle button in topbar (mobile)
            $('#customSidebarToggleTop').on('click', function(event) {
                event.preventDefault();
                event.stopPropagation();
                toggleSidebar();
            });

            handleResponsiveSidebar();

            $(window).on('resize', function() {
                handleResponsiveSidebar();
            });

            $sidebarOverlay.on('click', function() {
                closeMobileSidebar();
            });

            $('#sidebarCloseMobile').on('click', function(event) {
                event.preventDefault();
                event.stopPropagation();
                closeMobileSidebar();
            });

            if (!history.state || !history.state.url) {
                history.replaceState({ url: window.location.href }, '', window.location.href);
            }

            $('#accordionSidebar').on('click', '.nav-link', function(event) {
                if (event.ctrlKey || event.metaKey || event.shiftKey || event.which === 2 || this.target === '_blank') {
                    return;
                }

                const href = this.getAttribute('href');

                if (!href || href.startsWith('#')) {
                    return;
                }

                event.preventDefault();
                if (isMobileView()) {
                    closeMobileSidebar();
                }

                loadPage(this.href);
            });

            // Handle ajax form submissions inside pageContent to avoid full reload
            $('#pageContent').on('submit', 'form.page-form', function(event) {
                event.preventDefault();
                const form = this;
                const action = form.getAttribute('action') || window.location.href;
                const method = (form.getAttribute('method') || 'GET').toUpperCase();
                const formData = new FormData(form);
                const contentContainer = document.getElementById('pageContent');

                contentContainer?.classList.add('content-loading');

                fetch(action, {
                    method: method,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: method === 'GET' ? null : formData,
                }).then(response => {
                    if (response.redirected) {
                        window.location.href = response.url;
                        return null;
                    }
                    if (!response.ok) {
                        throw new Error('Failed to load content');
                    }
                    return response.text();
                }).then(html => {
                    if (!html) {
                        return;
                    }
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newContent = doc.getElementById('pageContent');

                    if (!newContent) {
                        window.location.href = action;
                        return;
                    }

                    contentContainer.innerHTML = newContent.innerHTML;
                    executePageScripts(contentContainer);

                    const newTitle = doc.querySelector('title');
                    if (newTitle) {
                        document.title = newTitle.textContent;
                    }

                    updateSidebarActiveState(doc);
                }).catch(() => {
                    window.location.href = action;
                }).finally(() => {
                    contentContainer?.classList.remove('content-loading');
                });
            });

            window.addEventListener('popstate', function(event) {
                if (event.state && event.state.url) {
                    closeMobileSidebar();
                    loadPage(event.state.url, false);
                }
            });

            const savedSidebarScroll = window.sessionStorage.getItem('sidebarScrollTop');
            if (savedSidebarScroll && document.querySelector('.sidebar-scroll')) {
                document.querySelector('.sidebar-scroll').scrollTop = Number(savedSidebarScroll);
            }

            window.addEventListener('pagehide', () => {
                const sidebar = document.querySelector('.sidebar-scroll');
                if (sidebar) {
                    window.sessionStorage.setItem('sidebarScrollTop', String(sidebar.scrollTop));
                }
            });
        });
    </script>
    @yield('scripts')

</body>

</html>
