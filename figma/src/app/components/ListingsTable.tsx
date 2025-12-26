interface Listing {
  console: string;
  condition: string;
  price: number;
  date: string;
  source: string;
}

interface ListingsTableProps {
  listings: Listing[];
}

export function ListingsTable({ listings }: ListingsTableProps) {
  return (
    <div className="bg-[#1a1f29] border border-[#2a2f39]">
      <div className="border-b border-[#2a2f39] p-4">
        <h2 className="text-white">Recent Sales</h2>
      </div>
      <div className="overflow-x-auto">
        <table className="w-full">
          <thead>
            <tr className="border-b border-[#2a2f39] text-left">
              <th className="p-3 text-[#6b7280] font-normal">Console</th>
              <th className="p-3 text-[#6b7280] font-normal">Condition</th>
              <th className="p-3 text-[#6b7280] font-normal">Price</th>
              <th className="p-3 text-[#6b7280] font-normal">Date</th>
              <th className="p-3 text-[#6b7280] font-normal">Source</th>
            </tr>
          </thead>
          <tbody>
            {listings.map((listing, index) => (
              <tr 
                key={index} 
                className="border-b border-[#2a2f39] last:border-b-0"
              >
                <td className="p-3 text-white">{listing.console}</td>
                <td className="p-3 text-[#9ca3af]">{listing.condition}</td>
                <td className="p-3 text-[#00ff88]">${listing.price.toFixed(2)}</td>
                <td className="p-3 text-[#9ca3af]">{listing.date}</td>
                <td className="p-3">
                  <a href="#" className="text-[#00d9ff] hover:underline">
                    {listing.source}
                  </a>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}
