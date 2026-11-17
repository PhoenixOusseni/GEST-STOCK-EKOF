<div class="inv-footer">
    <p>{{ $footerMessage ?? 'Merci de conserver ce document à titre de justificatif.' }}</p>
    <p>Édité le {{ now()->format('d/m/Y à H:i') }} — <strong>{{ $entete->titre ?? 'SAFRECO-GSM' }}</strong></p>
</div>
<div class="inv-accent-bar"></div>
