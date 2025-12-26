interface Stat {
  label: string;
  value: string;
  isPrice?: boolean;
}

interface StatsCardsProps {
  stats: Stat[];
}

export function StatsCards({ stats }: StatsCardsProps) {
  return (
    <div className="grid grid-cols-4 gap-4">
      {stats.map((stat, index) => (
        <div 
          key={index} 
          className="bg-[#1a1f29] border border-[#2a2f39] p-4"
        >
          <div className="text-[#6b7280] text-sm mb-1">{stat.label}</div>
          <div className={stat.isPrice ? 'text-[#00ff88]' : 'text-white'}>
            {stat.value}
          </div>
        </div>
      ))}
    </div>
  );
}
