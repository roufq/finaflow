import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'api_service.dart';
import 'login_screen.dart';
import 'transaction_screen.dart';

class DashboardScreen extends StatefulWidget {
  @override
  _DashboardScreenState createState() => _DashboardScreenState();
}

class _DashboardScreenState extends State<DashboardScreen> {
  Map<String, dynamic>? dashboardData;
  bool isLoading = true;
  final currencyFormat = NumberFormat.currency(locale: 'id', symbol: 'Rp ', decimalDigits: 0);

  @override
  void initState() {
    super.initState();
    loadData();
  }

  Future<void> loadData() async {
    setState(() => isLoading = true);
    try {
      final response = await ApiService.getDashboard();
      setState(() {
        dashboardData = response['data'];
        isLoading = false;
      });
    } catch (e) {
      setState(() => isLoading = false);
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Koneksi Gagal')));
    }
  }

  void logout() async {
    await ApiService.logout();
    Navigator.pushReplacement(context, MaterialPageRoute(builder: (context) => LoginScreen()));
  }

  @override
  Widget build(BuildContext context) {
    if (isLoading && dashboardData == null) {
      return const Scaffold(body: Center(child: CircularProgressIndicator()));
    }

    final user = dashboardData?['user'] ?? {'name': 'User'};
    final summary = dashboardData?['summary'] ?? {'total_cash': 0, 'total_income': 0, 'total_expense': 0};
    final analysis = dashboardData?['analysis'] ?? {'health_score': 0};
    final accounts = (dashboardData?['accounts'] as List?) ?? [];
    final recentTxs = (dashboardData?['recent_transactions'] as List?) ?? [];

    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FE),
      appBar: AppBar(
        title: Text('Halo, ${user['name']}', style: const TextStyle(fontWeight: FontWeight.bold)),
        backgroundColor: Colors.white,
        actions: [
          IconButton(icon: const Icon(Icons.logout, color: Colors.red), onPressed: logout),
        ],
      ),
      body: RefreshIndicator(
        onRefresh: loadData,
        child: SingleChildScrollView(
          physics: const AlwaysScrollableScrollPhysics(),
          padding: const EdgeInsets.all(20),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              _buildBalanceCard(summary),
              const SizedBox(height: 24),
              _buildHealthSection(analysis),
              const SizedBox(height: 24),
              const Text('Dompet Anda', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
              const SizedBox(height: 12),
              _buildAccountGrid(accounts),
              const SizedBox(height: 24),
              const Text('Transaksi Terakhir', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
              const SizedBox(height: 12),
              _buildTransactionList(recentTxs),
            ],
          ),
        ),
      ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () async {
          final result = await Navigator.push(context, MaterialPageRoute(builder: (context) => TransactionScreen()));
          if (result == true) loadData();
        },
        label: const Text('Catat', style: TextStyle(color: Colors.white)),
        icon: const Icon(Icons.add, color: Colors.white),
        backgroundColor: Colors.indigo[700],
      ),
    );
  }

  Widget _buildBalanceCard(Map<String, dynamic> summary) {
    return Container(
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        gradient: LinearGradient(colors: [Colors.indigo[700]!, Colors.indigo[400]!]),
        borderRadius: BorderRadius.circular(24),
      ),
      child: Column(
        children: [
          const Text('Total Saldo', style: TextStyle(color: Colors.white70)),
          Text(currencyFormat.format(summary['total_cash']), style: const TextStyle(color: Colors.white, fontSize: 32, fontWeight: FontWeight.bold)),
          const Divider(color: Colors.white24, height: 32),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              _smallInfo('Masuk', summary['total_income'], Colors.greenAccent),
              _smallInfo('Keluar', summary['total_expense'], Colors.redAccent),
            ],
          )
        ],
      ),
    );
  }

  Widget _smallInfo(String label, dynamic val, Color color) {
    return Column(children: [
      Text(label, style: const TextStyle(color: Colors.white60, fontSize: 12)),
      Text(currencyFormat.format(val), style: TextStyle(color: color, fontWeight: FontWeight.bold)),
    ]);
  }

  Widget _buildHealthSection(Map<String, dynamic> analysis) {
    return Card(
      elevation: 0,
      color: Colors.white,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16), border: Border.all(color: Colors.grey[200]!)),
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Row(
          children: [
            const Icon(Icons.favorite, color: Colors.pink, size: 32),
            const SizedBox(width: 16),
            Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
              const Text('Health Score', style: TextStyle(fontWeight: FontWeight.bold)),
              Text('Skor: ${analysis['health_score']}/100', style: const TextStyle(color: Colors.grey)),
            ]),
          ],
        ),
      ),
    );
  }

  Widget _buildAccountGrid(List accounts) {
    return GridView.builder(
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(crossAxisCount: 2, childAspectRatio: 2, crossAxisSpacing: 12, mainAxisSpacing: 12),
      itemCount: accounts.length,
      itemBuilder: (context, index) {
        final acc = accounts[index];
        return Container(
          padding: const EdgeInsets.all(12),
          decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(16), border: Border.all(color: Colors.grey[200]!)),
          child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
            Text(acc['name'], style: const TextStyle(fontSize: 12, color: Colors.grey)),
            const Spacer(),
            Text(currencyFormat.format(acc['balance']), style: const TextStyle(fontWeight: FontWeight.bold)),
          ]),
        );
      },
    );
  }

  Widget _buildTransactionList(List txs) {
    return Column(
      children: txs.map((tx) => Card(
        margin: const EdgeInsets.only(bottom: 8),
        child: ListTile(
          leading: CircleAvatar(backgroundColor: tx['type'] == 'income' ? Colors.green[50] : Colors.red[50], child: Icon(tx['type'] == 'income' ? Icons.south_west : Icons.north_east, color: tx['type'] == 'income' ? Colors.green : Colors.red, size: 18)),
          title: Text(tx['description'] ?? 'Transaksi', style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w600)),
          trailing: Text(currencyFormat.format(tx['amount']), style: TextStyle(color: tx['type'] == 'income' ? Colors.green : Colors.red, fontWeight: FontWeight.bold)),
        ),
      )).toList(),
    );
  }
}
