import { LineChart, Line, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer, Area, AreaChart } from 'recharts';

interface PriceChartProps {
  data: Array<{ date: string; price: number }>;
}

// Custom tooltip component
const CustomTooltip = ({ active, payload, label }: any) => {
  if (active && payload && payload.length) {
    return (
      <div className="bg-[#1a1f29] border border-[#2a2f39] px-3 py-2 shadow-lg">
        <div className="text-[#6b7280] text-xs mb-1">{label}</div>
        <div className="text-[#00ff88] font-medium">${payload[0].value.toFixed(2)}</div>
      </div>
    );
  }
  return null;
};

export function PriceChart({ data }: PriceChartProps) {
  return (
    <div className="bg-[#1a1f29] border border-[#2a2f39] p-4">
      <h2 className="mb-4 text-white">Price History</h2>
      <ResponsiveContainer width="100%" height={280}>
        <AreaChart data={data} margin={{ top: 5, right: 5, left: 5, bottom: 5 }}>
          <defs>
            <linearGradient id="priceGradient" x1="0" y1="0" x2="0" y2="1">
              <stop offset="0%" stopColor="#00ff88" stopOpacity={0.2} />
              <stop offset="100%" stopColor="#00ff88" stopOpacity={0} />
            </linearGradient>
          </defs>
          <CartesianGrid strokeDasharray="0" stroke="#2a2f39" vertical={false} />
          <XAxis 
            dataKey="date" 
            stroke="#2a2f39"
            tick={{ fill: '#6b7280', fontSize: 11 }}
            axisLine={{ stroke: '#2a2f39' }}
            tickLine={false}
          />
          <YAxis 
            stroke="#2a2f39"
            tick={{ fill: '#6b7280', fontSize: 11 }}
            tickFormatter={(value) => `$${value}`}
            axisLine={{ stroke: '#2a2f39' }}
            tickLine={false}
            width={50}
          />
          <Tooltip content={<CustomTooltip />} cursor={{ stroke: '#00ff88', strokeWidth: 1, strokeDasharray: '5 5' }} />
          <Area 
            type="monotone" 
            dataKey="price" 
            stroke="#00ff88" 
            strokeWidth={2}
            fill="url(#priceGradient)"
            dot={false}
            activeDot={{ r: 4, fill: '#00ff88', stroke: '#0f1419', strokeWidth: 2 }}
          />
        </AreaChart>
      </ResponsiveContainer>
    </div>
  );
}