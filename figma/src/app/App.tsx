import { PriceChart } from './components/PriceChart';
import { StatsCards } from './components/StatsCards';
import { ListingsTable } from './components/ListingsTable';

// Mock data for price history
const priceData = [
  { date: 'Jan', price: 89.50 },
  { date: 'Feb', price: 92.00 },
  { date: 'Mar', price: 88.75 },
  { date: 'Apr', price: 95.00 },
  { date: 'May', price: 98.50 },
  { date: 'Jun', price: 102.00 },
  { date: 'Jul', price: 99.75 },
  { date: 'Aug', price: 105.00 },
  { date: 'Sep', price: 108.50 },
  { date: 'Oct', price: 112.00 },
  { date: 'Nov', price: 115.50 },
  { date: 'Dec', price: 119.99 },
];

// Mock stats data
const stats = [
  { label: 'Current Price', value: '$119.99', isPrice: true },
  { label: '30-Day Avg', value: '$112.33', isPrice: true },
  { label: 'All-Time High', value: '$145.00', isPrice: true },
  { label: 'Total Sales', value: '1,247' },
];

// Mock listings data
const listings = [
  { console: 'Nintendo 64', condition: 'Complete in Box', price: 124.99, date: '12/20/2024', source: 'eBay' },
  { console: 'Nintendo 64', condition: 'Console Only', price: 89.99, date: '12/19/2024', source: 'Mercari' },
  { console: 'Nintendo 64', condition: 'Complete in Box', price: 135.00, date: '12/18/2024', source: 'eBay' },
  { console: 'Nintendo 64', condition: 'Loose', price: 79.99, date: '12/17/2024', source: 'Facebook' },
  { console: 'Nintendo 64', condition: 'Complete in Box', price: 119.99, date: '12/16/2024', source: 'eBay' },
  { console: 'Nintendo 64', condition: 'Console Only', price: 94.50, date: '12/15/2024', source: 'Mercari' },
  { console: 'Nintendo 64', condition: 'Loose', price: 75.00, date: '12/14/2024', source: 'Facebook' },
  { console: 'Nintendo 64', condition: 'Complete in Box', price: 129.99, date: '12/13/2024', source: 'eBay' },
];

// Console categories
const consoles = [
  'Nintendo 64',
  'Super Nintendo',
  'Nintendo Entertainment System',
  'Sega Genesis',
  'Sega Dreamcast',
  'PlayStation 1',
  'PlayStation 2',
  'GameCube',
  'Xbox',
  'Atari 2600',
];

export default function App() {
  return (
    <div className="min-h-screen bg-[#0f1419]">
      {/* Header */}
      <header className="border-b border-[#2a2f39]">
        <div className="max-w-[1200px] mx-auto px-6 py-4">
          <h1 className="text-white">RetroPrice</h1>
          <p className="text-[#6b7280] text-sm mt-1">Retro Console Price Reference</p>
        </div>
      </header>

      {/* Main Content */}
      <main className="max-w-[1200px] mx-auto px-6 py-8">
        {/* Console Navigation */}
        <nav className="mb-8 pb-4 border-b border-[#2a2f39]">
          <div className="flex flex-wrap gap-6">
            {consoles.map((console) => (
              <a 
                key={console}
                href="#" 
                className="text-[#00d9ff] hover:underline text-sm"
              >
                {console}
              </a>
            ))}
          </div>
        </nav>

        {/* Console Title */}
        <div className="mb-6">
          <h2 className="text-white mb-1">Nintendo 64</h2>
          <p className="text-[#6b7280]">Console • Released 1996</p>
        </div>

        {/* Stats Cards */}
        <div className="mb-6">
          <StatsCards stats={stats} />
        </div>

        {/* Price Chart */}
        <div className="mb-6">
          <PriceChart data={priceData} />
        </div>

        {/* Listings Table */}
        <ListingsTable listings={listings} />
      </main>

      {/* Footer */}
      <footer className="border-t border-[#2a2f39] mt-12">
        <div className="max-w-[1200px] mx-auto px-6 py-6">
          <div className="flex justify-between items-center">
            <div className="text-[#6b7280] text-sm">
              © 2024 RetroPrice. Price data for reference only.
            </div>
            <div className="flex gap-6">
              <a href="#" className="text-[#00d9ff] hover:underline text-sm">About</a>
              <a href="#" className="text-[#00d9ff] hover:underline text-sm">API</a>
              <a href="#" className="text-[#00d9ff] hover:underline text-sm">Contact</a>
            </div>
          </div>
        </div>
      </footer>
    </div>
  );
}
