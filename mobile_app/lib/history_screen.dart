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
    try {
      final data = await ApiService.getTransactions();
      setState(() { _transactions = data; _isLoading = false; });
    } catch (e) {
      setState(() => _isLoading = false);
    }
  }

  void _confirmDelete(int id) {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Hapus Transaksi?'),
        content: const Text('Data yang dihapus tidak bisa dikembalikan.'),
        actions: [
          TextButton(onPressed: () => Navigator.pop(context), child: const Text('Batal')),
          TextButton(onPressed: () async {
            Navigator.pop(context);
            final success = await ApiService.deleteTransaction(id);
            if (success) _fetchTransactions();
          }, child: const Text('Hapus', style: TextStyle(color: Colors.red))),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FE),
      appBar: AppBar(title: const Text('Riwayat Transaksi', style: TextStyle(fontWeight: FontWeight.bold))),
      body: RefreshIndicator(
        onRefresh: _fetchTransactions,
        child: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : _transactions.isEmpty
            ? ListView(children: const [SizedBox(height: 100), Center(child: Text('Belum ada transaksi'))])
            : ListView.builder(
                padding: const EdgeInsets.all(16),
                itemCount: _transactions.length,
                itemBuilder: (context, index) {
                  final tx = _transactions[index];
                  final isIncome = tx['type'] == 'income';
                  return Card(
                    elevation: 0,
                    margin: const EdgeInsets.only(bottom: 12),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16), border: Border.all(color: Colors.grey[200]!)),
                    child: ListTile(
                      contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 4),
                      leading: Icon(isIncome ? Icons.add_circle_outline : Icons.remove_circle_outline, color: isIncome ? Colors.green : Colors.red),
                      title: Text(tx['description'] ?? 'Transaksi', style: const TextStyle(fontWeight: FontWeight.bold)),
                      subtitle: Text('${tx['category']['name']} • ${tx['transaction_date']}'),
                      trailing: Row(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          Text(currencyFormat.format(tx['amount']), style: TextStyle(color: isIncome ? Colors.green : Colors.red, fontWeight: FontWeight.bold)),
                          const SizedBox(width: 8),
                          IconButton(icon: const Icon(Icons.close, size: 18, color: Colors.grey), onPressed: () => _confirmDelete(tx['id'])),
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
