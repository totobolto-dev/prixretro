@extends('layout')

@section('title')
Tendances du Marché Retrogaming | PrixRetro
@endsection

@section('content')
<div class="container">
    <div class="breadcrumb">
        <a href="/">Accueil</a>
        <span>›</span>
        <span>Tendances du Marché</span>
    </div>

    <h1>📊 Tendances du Marché</h1>

    <div style="background: var(--bg-card); border-radius: var(--radius); border: 1px solid var(--border-color); padding: 2rem; margin-bottom: 2rem;">
        <p style="font-size: 1.1rem; color: var(--text-secondary); margin-bottom: 1.5rem;">
            Analyse des variations de prix sur les 30 derniers jours comparés aux 30 jours précédents.
            Découvrez quelles consoles gagnent ou perdent de la valeur sur le marché de l'occasion.
        </p>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
            <div style="background: var(--bg-darker); padding: 1.5rem; border-radius: var(--radius);">
                <div style="font-size: 0.9rem; color: var(--text-secondary); margin-bottom: 0.5rem;">Variantes analysées</div>
                <div style="font-size: 2rem; font-weight: 700;">{{ $marketStats['total_variants'] }}</div>
            </div>

            <div style="background: var(--bg-darker); padding: 1.5rem; border-radius: var(--radius);">
                <div style="font-size: 0.9rem; color: var(--text-secondary); margin-bottom: 0.5rem;">Variation moyenne</div>
                <div style="font-size: 2rem; font-weight: 700; color: {{ $marketStats['avg_change'] >= 0 ? '#10b981' : '#ef4444' }};">
                    {{ $marketStats['avg_change'] >= 0 ? '+' : '' }}{{ number_format($marketStats['avg_change'], 1) }}%
                </div>
            </div>

            <div style="background: var(--bg-darker); padding: 1.5rem; border-radius: var(--radius);">
                <div style="font-size: 0.9rem; color: var(--text-secondary); margin-bottom: 0.5rem;">En hausse</div>
                <div style="font-size: 2rem; font-weight: 700; color: #10b981;">
                    {{ $marketStats['gainers_count'] }}
                    <span style="font-size: 1.2rem; opacity: 0.7;">({{ number_format($marketStats['gainers_percentage'], 0) }}%)</span>
                </div>
            </div>

            <div style="background: var(--bg-darker); padding: 1.5rem; border-radius: var(--radius);">
                <div style="font-size: 0.9rem; color: var(--text-secondary); margin-bottom: 0.5rem;">En baisse</div>
                <div style="font-size: 2rem; font-weight: 700; color: #ef4444;">
                    {{ $marketStats['losers_count'] }}
                </div>
            </div>
        </div>
    </div>

    {{-- Top Gainers --}}
    @if(count($topGainers) > 0)
    <div style="margin-bottom: 3rem;">
        <h2 style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem;">
            <span style="font-size: 2rem;">📈</span>
            Top 10 des Hausses
        </h2>

        <div style="background: var(--bg-card); border-radius: var(--radius); border: 1px solid var(--border-color); overflow: hidden;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead style="background: var(--bg-darker); border-bottom: 1px solid var(--border-color);">
                    <tr>
                        <th style="padding: 1rem; text-align: left; font-weight: 600;">Rang</th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600;">Console</th>
                        <th style="padding: 1rem; text-align: right; font-weight: 600;">Prix Avant</th>
                        <th style="padding: 1rem; text-align: right; font-weight: 600;">Prix Actuel</th>
                        <th style="padding: 1rem; text-align: right; font-weight: 600;">Variation</th>
                        <th style="padding: 1rem; text-align: center; font-weight: 600;">Ventes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topGainers as $index => $trend)
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 1rem; font-weight: 700; font-size: 1.2rem; color: var(--text-secondary);">
                            #{{ $index + 1 }}
                        </td>
                        <td style="padding: 1rem;">
                            <a href="/{{ $trend['console']->slug }}/{{ $trend['variant']->slug }}" style="color: var(--accent-primary); text-decoration: none; font-weight: 500;">
                                {{ $trend['variant']->display_name }}
                            </a>
                            <div style="font-size: 0.85rem; color: var(--text-secondary); margin-top: 0.25rem;">
                                {{ $trend['console']->name }}
                            </div>
                        </td>
                        <td style="padding: 1rem; text-align: right; font-family: monospace; color: var(--text-secondary);">
                            {{ number_format($trend['previous_avg'], 0) }}€
                        </td>
                        <td style="padding: 1rem; text-align: right; font-family: monospace; font-weight: 600; font-size: 1.1rem;">
                            {{ number_format($trend['current_avg'], 0) }}€
                        </td>
                        <td style="padding: 1rem; text-align: right;">
                            <div style="display: inline-flex; align-items: center; gap: 0.5rem; background: #d1fae5; color: #059669; padding: 0.5rem 1rem; border-radius: 9999px; font-weight: 700;">
                                <span style="font-size: 1.2rem;">↑</span>
                                <span>+{{ number_format($trend['change_percentage'], 1) }}%</span>
                            </div>
                            <div style="font-size: 0.85rem; color: var(--text-secondary); margin-top: 0.5rem;">
                                +{{ number_format($trend['change_amount'], 0) }}€
                            </div>
                        </td>
                        <td style="padding: 1rem; text-align: center; font-family: monospace; color: var(--text-secondary);">
                            {{ $trend['recent_sales'] }}/{{ $trend['total_sales'] }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Top Losers --}}
    @if(count($topLosers) > 0)
    <div style="margin-bottom: 3rem;">
        <h2 style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem;">
            <span style="font-size: 2rem;">📉</span>
            Top 10 des Baisses
        </h2>

        <div style="background: var(--bg-card); border-radius: var(--radius); border: 1px solid var(--border-color); overflow: hidden;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead style="background: var(--bg-darker); border-bottom: 1px solid var(--border-color);">
                    <tr>
                        <th style="padding: 1rem; text-align: left; font-weight: 600;">Rang</th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600;">Console</th>
                        <th style="padding: 1rem; text-align: right; font-weight: 600;">Prix Avant</th>
                        <th style="padding: 1rem; text-align: right; font-weight: 600;">Prix Actuel</th>
                        <th style="padding: 1rem; text-align: right; font-weight: 600;">Variation</th>
                        <th style="padding: 1rem; text-align: center; font-weight: 600;">Ventes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topLosers as $index => $trend)
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 1rem; font-weight: 700; font-size: 1.2rem; color: var(--text-secondary);">
                            #{{ $index + 1 }}
                        </td>
                        <td style="padding: 1rem;">
                            <a href="/{{ $trend['console']->slug }}/{{ $trend['variant']->slug }}" style="color: var(--accent-primary); text-decoration: none; font-weight: 500;">
                                {{ $trend['variant']->display_name }}
                            </a>
                            <div style="font-size: 0.85rem; color: var(--text-secondary); margin-top: 0.25rem;">
                                {{ $trend['console']->name }}
                            </div>
                        </td>
                        <td style="padding: 1rem; text-align: right; font-family: monospace; color: var(--text-secondary);">
                            {{ number_format($trend['previous_avg'], 0) }}€
                        </td>
                        <td style="padding: 1rem; text-align: right; font-family: monospace; font-weight: 600; font-size: 1.1rem;">
                            {{ number_format($trend['current_avg'], 0) }}€
                        </td>
                        <td style="padding: 1rem; text-align: right;">
                            <div style="display: inline-flex; align-items: center; gap: 0.5rem; background: #fee2e2; color: #dc2626; padding: 0.5rem 1rem; border-radius: 9999px; font-weight: 700;">
                                <span style="font-size: 1.2rem;">↓</span>
                                <span>{{ number_format($trend['change_percentage'], 1) }}%</span>
                            </div>
                            <div style="font-size: 0.85rem; color: var(--text-secondary); margin-top: 0.5rem;">
                                {{ number_format($trend['change_amount'], 0) }}€
                            </div>
                        </td>
                        <td style="padding: 1rem; text-align: center; font-family: monospace; color: var(--text-secondary);">
                            {{ $trend['recent_sales'] }}/{{ $trend['total_sales'] }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <div style="background: var(--bg-card); border-radius: var(--radius); border: 1px solid var(--accent-primary); padding: 1.5rem; margin-top: 3rem;">
        <h3 style="margin: 0 0 1rem 0;">💡 Comment utiliser ces données ?</h3>
        <ul style="margin: 0; padding-left: 1.5rem; color: var(--text-secondary); line-height: 1.8;">
            <li><strong>Hausses :</strong> Consoles gagnant de la valeur. Bon moment pour vendre si vous en possédez une.</li>
            <li><strong>Baisses :</strong> Opportunités d'achat si vous cherchez une console spécifique.</li>
            <li><strong>Tendances :</strong> Les variations reflètent la demande réelle du marché sur eBay France.</li>
            <li><strong>Volume :</strong> Plus il y a de ventes récentes, plus la tendance est fiable.</li>
        </ul>
    </div>
</div>
@endsection
