class Activity
{
  constructor ()
  {
    if (arguments.length == 1 && typeof arguments[0] == 'object')
    {
      this.id = arguments[0]["id"];
      this.title = arguments[0]["titolo"];
      this.description = arguments[0]["descrizione"];
      this.date = arguments[0]["data_creazione"];
      this.leftPositions = arguments[0]["posti_rimanenti"];
      this.completed = arguments[0]["completata"];
      this.categories = arguments[0]["categorie"];
      this.teammates = arguments[0]["partecipanti"];
      this.locationid = arguments[0]["idPosizione"];
    }

    else
    {
      this.id = null;
      this.title = null;
      this.description = null;
      this.date = null;
      this.leftPositions = null;
      this.completed = null;
      this.categories = null;
      this.teammates = null;
      this.locationid = null;
    }
  }
}
