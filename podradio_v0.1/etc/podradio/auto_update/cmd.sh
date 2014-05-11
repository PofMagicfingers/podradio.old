#!/bin/sh
rm /var/medias/partenaires/vrac/auto/*
rm /var/medias/play/prime/auto/*
echo "RSS Update [partenaires] : "
echo ''
echo "J'ai viré le vieux vrac auto et le vieux prime auto."
echo ''
echo "J'ai téléchargé http://feedproxy.google.com/~r/lerendezvoustech/~5/uU4eS1a5dVI/techep19.mp3." && wget -q http://feedproxy.google.com/~r/lerendezvoustech/~5/uU4eS1a5dVI/techep19.mp3 -O techep19.mp3 && mv techep19.mp3 /var/medias/play/prime/auto/techep19.mp3
echo "J'ai téléchargé http://feedproxy.google.com/~r/lepodcasthightechfrance/~5/RHWD1Ax0bsE/Episode42.mp3." && wget -q http://feedproxy.google.com/~r/lepodcasthightechfrance/~5/RHWD1Ax0bsE/Episode42.mp3 -O Episode42.mp3 && mv Episode42.mp3 /var/medias/play/prime/auto/Episode42.mp3
echo "J'ai téléchargé http://feedproxy.google.com/~r/lepodcasthightechfrance/~5/dTULgycK4y0/Episode41.mp3." && wget -q http://feedproxy.google.com/~r/lepodcasthightechfrance/~5/dTULgycK4y0/Episode41.mp3 -O Episode41.mp3 && mv Episode41.mp3 /var/medias/partenaires/vrac/auto/Episode41.mp3
echo "J'ai téléchargé http://feedproxy.google.com/~r/linaudible/~5/1zPqgMzbvuk/baltringues.mp3." && wget -q http://feedproxy.google.com/~r/linaudible/~5/1zPqgMzbvuk/baltringues.mp3 -O baltringues.mp3 && mv baltringues.mp3 /var/medias/partenaires/vrac/auto/baltringues.mp3
echo "J'ai téléchargé http://feedproxy.google.com/~r/linaudible/~5/iZy40cMCH3U/wwsh013.mp3." && wget -q http://feedproxy.google.com/~r/linaudible/~5/iZy40cMCH3U/wwsh013.mp3 -O wwsh013.mp3 && mv wwsh013.mp3 /var/medias/partenaires/vrac/auto/wwsh013.mp3
echo "J'ai téléchargé http://feedproxy.google.com/~r/linaudible/~5/OMnowHHWtyY/casetwitte.mp3." && wget -q http://feedproxy.google.com/~r/linaudible/~5/OMnowHHWtyY/casetwitte.mp3 -O casetwitte.mp3 && mv casetwitte.mp3 /var/medias/partenaires/vrac/auto/casetwitte.mp3
echo "J'ai téléchargé http://feedproxy.google.com/~r/linaudible/~5/oLn_8X5fmh0/wwsh012.mp3." && wget -q http://feedproxy.google.com/~r/linaudible/~5/oLn_8X5fmh0/wwsh012.mp3 -O wwsh012.mp3 && mv wwsh012.mp3 /var/medias/partenaires/vrac/auto/wwsh012.mp3
echo "J'ai téléchargé http://www.declencheur.com/clic/medias/2009/decl-2009-10-19.mp3." && wget -q http://www.declencheur.com/clic/medias/2009/decl-2009-10-19.mp3 -O decl-2009-10-19.mp3 && mv decl-2009-10-19.mp3 /var/medias/partenaires/vrac/auto/decl-2009-10-19.mp3
echo "J'ai téléchargé http://www.declencheur.com/clic/medias/2009/decl-2009-10-12.mp3." && wget -q http://www.declencheur.com/clic/medias/2009/decl-2009-10-12.mp3 -O decl-2009-10-12.mp3 && mv decl-2009-10-12.mp3 /var/medias/partenaires/vrac/auto/decl-2009-10-12.mp3
