def parite():
    n = int(input("Entrez un entier strictement positif :"))
    while n < 1:
        n = int(input("Entrez un entier STRICTEMENT POSITIF, s.v.p. :"))
    if n % 2 == 0:
        return  "est pair"
    else:
        return "est impair"
