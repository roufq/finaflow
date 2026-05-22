import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'api_service.dart';

class HistoryScreen extends StatefulWidget {
  @override
  _HistoryScreenState createState() => _HistoryScreenState();
}

class _HistoryScreenState extends State<HistoryScreen> {
  List<dynamic> _transactions = [];
  bool _isLoading = true;
  final currencyFormat = NumberFormat.currency(locale: 'id', symbol: 'Rp ', decimalDigits: 0);

  @override
  void initState() {
    super.initState();
    _fetchTransactions();
  }

  Future<void> _fetchTransactions() async {
    setState(() => _isLoading = true);
    final data = await ApiService.getTransactions();
    setState(() {
      _transactions = data;
      _isLoading = false;
    });
  }

  void _deleteTransaction(int id) async {
    final success = await ApiService.deleteTransaction(id);
    if (success) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Transaksi dihapus')));
      _fetchTransactions();
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Riwayat Transaksi', style: TextStyle(fontWeight: FontWeight.bold))),
      body: RefreshIndicator(
        onRefresh: _fetchTransactions,
        child: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : _transactions.isEmpty
            ? const Center(child: Text('Belum ada transaksi'))
            : ListView.builder(
                padding: const EdgeInsets.all(16),
                itemCount: _transactions.length,
                itemBuilder: (context, index) {
                  final tx = _transactions[index];
                  final isIncome = tx['type'] == 'income';
                  return Card(
                    margin: const EdgeInsets.only(bottom: 12),
                    child: ListTile(
                      leading: Icon(isIncome ? Icons.download : Icons.upload, color: isIncome ? Colors.green : Colors.red),
                      title: Text(tx['description'] ?? 'Tanpa Keterangan'),
                      subtitle: Text('${tx['category']['name']} • ${tx['transaction_date']}'),
                      trailing: Row(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          Text(currencyFormat.format(tx['amount']), style: TextStyle(color: isIncome ? Colors.green : Colors.red, fontWeight: FontWeight.bold)),
                          IconButton(icon: const Icon(Icons.delete_outline, size: 20), onPressed: () => _deleteTransaction(tx['id'])),
                        ],
                      ),
                    ),
                  );
                },
              ),
      ),
    );
  }
}
